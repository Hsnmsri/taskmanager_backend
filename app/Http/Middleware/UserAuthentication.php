<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Carbon\Carbon;
use App\Models\AccessToken;
use Illuminate\Http\Request;
use App\Classes\ResponseBodyBuilder;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class UserAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $accessTokens = AccessToken::where("user_id", $request->user_id)->get();
        foreach ($accessTokens as $key => $value) {
            try {
                if (Carbon::now()->gt(Carbon::parse($value->expire_time))) {
                    $accessTokens[$key]->delete();
                }
            } catch (Exception $error) {
                return response()->json(ResponseBodyBuilder::buildFailureResponse(0, $error->getMessage()));
            }
        }
        if (AccessToken::where("user_id", $request->user_id)->where("token", $request->token)->count() == 0) {
            return response()->json(ResponseBodyBuilder::buildFailureResponse(6));
        }
        return $next($request);
    }
}
