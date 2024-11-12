<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\fcm_token;
use App\Models\token;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $request)
    {

        if ($request->header('userToken')) {
            $token = token::where('token', $request->header('userToken'))->first();
            if (isset($token)) {
                $apiKey = $request->header('api-key');

                // تحقق من أن apiKey ليس فارغًا
                if ($apiKey && strlen($apiKey) > 4) {
                    // حذف أول حرفين وآخر حرفين
                    $apiKey = substr($apiKey, 2, -2);
                }
                if ($apiKey !== $token->key) {
                    return response()->json(['message' => 'Unauthorized'], 401);
                }
                $user = User::where('token_id', $token->id)->first();
                if (isset($user)) {
                    return response()->json('انت مسجل بالفعل', 401);
                } else {


                    $validator = Validator::make($request->all(), [
                        'f_name' => 'required|string|max:255',
                        'l_name' => 'required|string|max:255',
                        'email' => 'required|string|email|max:255|unique:users',
                        'phone' => 'required|string|max:10|unique:users',
                        'password' => 'required|string|min:8|confirmed',
                    ]);

                    // التحقق مما إذا كانت البيانات غير صالحة
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => 'error',
                            'errors' => $validator->errors()
                        ], 422); // إرسال رد يحتوي على الأخطاء
                    }

                    $user = User::create([
                        'f_name' => $request->f_name,
                        'l_name' => $request->l_name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'password' => Hash::make($request->password),
                        'token_id' => $token->id
                    ]);

                    if ($request->fcm_token) {
                        $fcm = fcm_token::where('fcm_token', $request->fcm_token)->first();
                        if (!isset($fcm)) {
                            fcm_token::create([
                                'token_id' => $token->id,
                                'fcm_token' => $request->fcm_token,
                            ]);
                        }
                    }

                    $user_info = User::with('token')->where('id', $user->id)->first();
                    $user_info->token()->first()->update(['islogin' => 1]);

                    $user_info = [
                        'f_name' => $request->f_name,
                        'l_name' => $request->l_name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'token' => $request->header('userToken')
                    ];

                    return response()->json($user_info, 200);
                }
            } else {
                return response()->json(['message' => 'token not found'], 404);
            }
        }
    }

    public function login(Request $request)
    {
        if ($request->header('userToken')) {
            $token = token::where('token', $request->header('userToken'))->first();
            if (isset($token)) {
                $apiKey = $request->header('api-key');

                // تحقق من أن apiKey ليس فارغًا
                if ($apiKey && strlen($apiKey) > 4) {
                    // حذف أول حرفين وآخر حرفين
                    $apiKey = substr($apiKey, 2, -2);
                }
                if ($apiKey !== $token->key) {
                    return response()->json(['message' => 'Unauthorized'], 401);
                }

                $validator = Validator::make($request->all(), [
                    'phone' => 'required',
                    'password' => 'required|string|min:8',
                ]);

                // التحقق مما إذا كانت البيانات غير صالحة
                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'error',
                        'errors' => $validator->errors()
                    ], 422); // إرسال رد يحتوي على الأخطاء
                }


                $user = User::where('phone', $request->phone)->first();

                if (!$user || !Hash::check($request->password, $user->password)) {
                    return response()->json(['message' => 'user not found'], 404);
                }

                $user_info = User::with('token')->where('id', $user->id)->first();
                if ($request->fcm_token) {
                    $fcm = fcm_token::where('fcm_token', $request->fcm_token)->first();
                    if (!isset($fcm)) {
                        fcm_token::create([
                            'token_id' => $user->token()->first()->id,
                            'fcm_token' => $request->fcm_token,
                        ]);
                    }
                }
                $user_info->token()->first()->update(['islogin' => 1]);
                $user_info = [
                    'f_name' => $user_info->f_name,
                    'l_name' => $user_info->l_name,
                    'email' => $user_info->email,
                    'phone' => $user_info->phone,
                    'token' => $user_info->token()->first()->token
                ];

                return response()->json($user_info, 200);
            }
        }
    }

    public function profile(Request $request)
    {

        if ($request->header('userToken')) {
            $token = token::where('token', $request->header('userToken'))->first();
            $apiKey = $request->header('api-key');

            // تحقق من أن apiKey ليس فارغًا
            if ($apiKey && strlen($apiKey) > 4) {
                // حذف أول حرفين وآخر حرفين
                $apiKey = substr($apiKey, 2, -2);
            }
            if ( $apiKey !== $token->key) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            $user_info = User::with('token')->where('token_id', $token->id)->first();

            $user_info = [
                'f_name' => $user_info->f_name,
                'l_name' => $user_info->l_name,
                'email' => $user_info->email,
                'phone' => $user_info->phone,
                'point' => $user_info->point,
                'notification' => $user_info->notification,
                'quiz' => $user_info->quiz,
                'token' => $user_info->token()->first()->token
            ];

            return response()->json($user_info, 200);
        }
    }
    public function update_profile(Request $request)
    {
        if ($request->header('api-key') !== 'your-secret-key') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        if ($request->header('userToken')) {
            $token = token::where('token', $request->header('userToken'))->first();
            $user_info = User::with('token')->where('token_id', $token->id)->first();
            $user_info->update([
                'f_name' => $request->f_name ?? $user_info->f_name,
                'l_name' => $request->l_name ?? $user_info->l_name,
                'email' => $request->email ?? $user_info->email,
                'password' => Hash::make($request->password) ?? $user_info->password
            ]);

            $user_info = [
                'f_name' => $user_info->f_name,
                'l_name' => $user_info->l_name,
                'email' => $user_info->email,
                'phone' => $user_info->phone,
                'point' => $user_info->point,
                'notification' => $user_info->notification,
                'quiz' => $user_info->quiz,
                'token' => $user_info->token()->first()->token
            ];

            return response()->json($user_info, 200);
        }
    }
}
