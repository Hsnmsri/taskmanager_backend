<?php

namespace App\Http\Controllers;

use App\Classes\ResponseBodyBuilder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        if (!$request->has("email") || !$request->has("password")) {
            return ResponseBodyBuilder::buildFailureResponse(3);
        }

        if (empty($request->email) || empty($request->password)) {
            return ResponseBodyBuilder::buildFailureResponse(4);
        }

        $user = User::where("email", $request->email);
        if ($user->count() != 0) {
            $user = $user->first();
            if (!Hash::check($request->password, $user->password)) {
                return ResponseBodyBuilder::buildFailureResponse(1);
            }
        }
    }
}
