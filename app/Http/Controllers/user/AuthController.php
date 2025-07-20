<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // تسجيل مستخدم جديد باستخدام رقم الهاتف
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|unique:users,phone',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('user-token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل المستخدم بنجاح',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    // تسجيل الدخول باستخدام رقم الهاتف
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|exists:users,phone',
            'password' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
        }

        $token = $user->createToken('user-token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => $user,
            'token' => $token
        ]);
    }

    // تسجيل الخروج
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'تم تسجيل الخروج']);
    }
}
