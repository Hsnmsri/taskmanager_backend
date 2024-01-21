<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\RecoveryMail;
use App\Models\AccessToken;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RecoveryToken;
use App\Classes\ResponseBodyBuilder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function login(Request $request)
    {
        if (!$request->has("email") || !$request->has("password")) {
            return ResponseBodyBuilder::buildFailureResponse(3); // requirement field not set
        }

        if (empty($request->email) || empty($request->password)) {
            return ResponseBodyBuilder::buildFailureResponse(4); // requirement field is empty
        }

        $user = User::where("email", $request->email);
        if ($user->count() != 0) {
            $user = $user->first();
            if (!Hash::check($request->password, $user->password)) {
                return ResponseBodyBuilder::buildFailureResponse(1); // password invalid
            }
            RecoveryToken::where("id", $user->id)->delete();
        }

        if ($user->count() == 0) {
            try {
                $user = new User();
                $user->email = trim($request->email);
                $user->password = Hash::make($request->password);
                $user->role = 1;
                if (!$user->save()) {
                    return ResponseBodyBuilder::buildFailureResponse(5); // save failed
                }
            } catch (Exception $error) {
                return ResponseBodyBuilder::buildFailureResponse(0, $error->getMessage());
            }
        }

        $this->updateUserLastLogin($user->id);
        $user_token = $this->createUserAccessToken($user->id);
        $this->clearUserAccessToken($user->id);
        return ResponseBodyBuilder::buildSuccessResponse(null, ["user_id" => $user->id, "token" => $user_token]);
    }

    public function requestResetPassword(Request $request)
    {
        if (!$request->has("email")) {
            return ResponseBodyBuilder::buildFailureResponse(3); // requirement field not set
        }

        if (empty($request->email)) {
            return ResponseBodyBuilder::buildFailureResponse(4); // requirement field is empty
        }

        try {
            $user = User::where("email", $request->email);
            if ($user->count() != 0) {
                $user = $user->first();
                RecoveryToken::where("id", $user->id)->delete();
                $recoveryTokenText = $this->createUserRecoveryToken($user->id);
                // send mail
                Mail::to($user->email)->send(new RecoveryMail($user->first_name, $recoveryTokenText));
                return ResponseBodyBuilder::buildSuccessResponse(null);
            }
        } catch (Exception $error) {
            return ResponseBodyBuilder::buildFailureResponse(0, $error->getMessage());
        }
    }

    /**
     * Create a new access token for the user.
     *
     * @param  int  $userId
     * @return string|mixed  The generated access token or a failure response.
     */
    private function createUserAccessToken($user_id)
    {
        try {
            $accessToken = new AccessToken();
            $accessToken->user_id = $user_id;
            $accessToken->token = Str::random(env("TOKEN_STRING_LENGTH"));
            $accessToken->expire_at = Carbon::now()->addMinutes(env("USER_SESSION_LIFETIME"))->toDateTimeString();
            if (!$accessToken->save()) {
                return ResponseBodyBuilder::buildFailureResponse(5); // save failed
            }
            return $accessToken->token;
        } catch (Exception $error) {
            return ResponseBodyBuilder::buildFailureResponse(0, $error->getMessage()); // server error
        }
    }

    /**
     * Clear expired access tokens for a given user.
     *
     * @param  int  $userId  The ID of the user.
     * @return void
     */
    private function clearUserAccessToken($user_id)
    {
        $accessTokens = AccessToken::where("user_id", $user_id)->get();
        foreach ($accessTokens as $key => $value) {
            try {
                if (Carbon::now()->gt(Carbon::parse($value->expire_time))) {
                    $accessTokens[$key]->delete();
                }
            } catch (Exception $error) {
                return ResponseBodyBuilder::buildFailureResponse(0, $error->getMessage());
            }
        }
    }

    /**
     * Update the last logged-in timestamp for a user.
     *
     * @param  int  $userId  The ID of the user.
     * @return void
     */
    private function updateUserLastLogin($user_id)
    {
        try {
            $user = User::find($user_id);
            $user->last_loggedin_at = Carbon::now()->toDateTimeString();
            $user->update();
        } catch (Exception $error) {
            $user->last_loggedin_at = $user->updated_at;
            $user->update();
            return ResponseBodyBuilder::buildFailureResponse(0, $error->getMessage());
        }
    }

    /**
     * Create a recovery token for a user to facilitate account recovery.
     *
     * @param  int  $userId  The ID of the user for whom the recovery token is created.
     * @return string|mixed  The generated recovery token or a failure response.
     */
    private function createUserRecoveryToken($user_id)
    {
        try {
            $recoveryToken = new RecoveryToken();
            $recoveryToken->user_id = $user_id;
            $recoveryToken->token = Str::random(env("TOKEN_STRING_LENGTH") * 2);
            $recoveryToken->expire_at = Carbon::now()->addMinutes(env("USER_SESSION_LIFETIME"))->toDateTimeString();
            if (!$recoveryToken->save()) {
                return ResponseBodyBuilder::buildFailureResponse(5); // save failed
            }
            return $recoveryToken->token;
        } catch (Exception $error) {
            return ResponseBodyBuilder::buildFailureResponse(0, $error->getMessage()); // server error
        }
    }
}
