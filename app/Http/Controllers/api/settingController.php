<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\city;
use App\Models\contact_us;
use App\Models\fcm_token;
use App\Models\setting;
use App\Models\token;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class settingController extends Controller
{
    public function get_setting(Request $request)
    {
        // استرجاع اللغة من الهيدر (header)
        $language = $request->header('lang', 'en'); // القيمة الافتراضية هي 'en'

        if ($request->fcm_token) {
            // التحقق من fcm token اذا موجود
            $fcm_token = fcm_token::where('fcm_token', $request->fcm_token)->first();

            if (!isset($fcm_token)) {
                // التحقق من ارسال ال token
                $token_new = Str::random(64);
                $key = Str::random(10);

                // تحقق من وجود token سابق، إذا لم يوجد يتم إنشاؤه
                $token = Token::firstOrCreate(
                    ['token' => $token_new],
                    ['isskip' => 0, 'islogin' => 0, 'key' => $key, 'key_updated_at' => now()]
                );

                // حفظ fcm_token مع token_id
                fcm_token::create([
                    'token_id' => $token->id,
                    'fcm_token' => $request->fcm_token,
                ]);
            } else {
                $token = token::where('id', $fcm_token->token_id)->first();
            }

            $token->update(['isskip' => 1]);
        } else {
            $token = token::where('token', $request->userToken)->first();
        }

        if ($token->key_updated_at === null || \Carbon\Carbon::parse($token->key_updated_at)->lt(now()->subDay())) {
            $token->update([
                'key' => Str::random(10),
                'key_updated_at' => now()
            ]);
        }
        // جلب المدن حسب اللغة المرسلة
        $cities = city::select('id')
            ->where('status', 'active')
            ->get()
            ->map(function ($city) use ($language) {
                // هنا يمكنك استخدام عملية الترجمة حسب اللغة
                $translation = $city->translations()->where('locale', $language)->first();
                return [
                    'id' => $city->id,
                    'name' => $translation ? $translation->name : $city->name, // استخدم الاسم من الترجمة أو الاسم الافتراضي
                ];
            });

        $setting = setting::with('translations')->where('status', 'active')->first();
        $background = $setting->translations()->where('locale', $language)->where('title', 'Background')->first();

        $tokenResponse = [
            'userToken' => $token->token,
            'isskip' => $token->isskip,
            'islogin' => $token->islogin,
            'fromApp' => $token->key,
            'cities' => $cities,
            'backgroundImage' => $background ? $background->description : '',
        ];

        return response()->json($tokenResponse, 200);
    }


    public function privacy_policy(Request $request){
        $language = $request->header('lang', 'en'); // القيمة الافتراضية هي 'en'
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


        $setting = setting::with('translations')->first();
        $privacy = $setting->translations()->where('locale', $language)->where('title','Privacy & Policy')->first();

        return response()->json(['text'=>$privacy->description],200);
            }}
    }

    public function term_condition(Request $request){
        $language = $request->header('lang', 'en'); // القيمة الافتراضية هي 'en'

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

        $setting = setting::with('translations')->first();
        $term = $setting->translations()->where('locale', $language)->where('title','Terms & Conditions')->first();

        return response()->json(['text'=>$term->description],200);
            }}
    }

    public function about_us(Request $request){
        $language = $request->header('lang', 'en'); // القيمة الافتراضية هي 'en'

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

        $setting = setting::with('translations')->first();
        $about = $setting->translations()->where('locale', $language)->where('title','About Us')->first();

        return response()->json(['text'=>$about->description],200);
            }}
    }

    public function contact_us(Request $request){
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
            'name' => 'required|string',
            'email' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|min:30',
        ]);

        // التحقق مما إذا كانت البيانات غير صالحة
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422); // إرسال رد يحتوي على الأخطاء
        }

        $contact = new contact_us();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->subject = $request->subject;
        $contact->message = $request->message;
        $contact->save();

        return response()->json($contact,200);
    }}
    }

    public function change_notification(Request $request){

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

            $user_info = User::with('token')->where('token_id', $token->id)->first();
            $user_info->update(['notification'=>1]);
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
        }}
    }
}
