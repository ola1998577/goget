<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\order;
use App\Models\token;
use App\Models\User;
use Illuminate\Http\Request;

class orderController extends Controller
{
    public function my_order(Request $request)
    {
        // التحقق من userToken
        if ($request->header('userToken')) {
            $token = token::where('token', $request->header('userToken'))->first();

            // التأكد من أن التوكن موجود
            if (!$token) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $apiKey = $request->header('api-key');

            // تحقق من أن apiKey ليس فارغًا
            if ($apiKey && strlen($apiKey) > 4) {
                // حذف أول حرفين وآخر حرفين
                $apiKey = substr($apiKey, 2, -2);
            }

            // التحقق من مطابقة apiKey مع key في التوكن
            if ($apiKey !== $token->key) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $user_info = User::with('token')->where('token_id', $token->id)->first();

            if ($user_info) {
                // جلب الطلبات مع تفاصيل المنتجات عبر الجدول الوسيط order_products
                $ordersQuery = order::with([
                    'products' => function ($query) {
                        // إحضار العلاقات المرتبطة بالمنتجات مثل المتجر والتصنيف والتقييم والصور والحجم واللون
                        $query->with('store', 'category', 'review', 'image', 'size', 'color');
                    },
                    'color',
                    'size',
                    'location'
                ])->where('user_id', $user_info->id);

                // تطبيق فلتر الحالة
                if ($request->filled('status')) {
                    $ordersQuery->where('status', $request->status);
                } else {
                    $ordersQuery->where('status', 'in_progress');
                }

                // الحصول على النتائج مع التصفية
                $orders = $ordersQuery->paginate(5);

                return response()->json($orders, 200);
            }
        }

        return response()->json(['message' => 'User not found or unauthorized'], 401);
    }


public function createOrder(Request $request)
{
    // قم بإنشاء الطلب
    $order = order::create([
        'user_id' => $request->user_id,
        // إضافة بيانات أخرى حسب الحاجة
    ]);

    // إضافة المنتجات إلى الطلب
    foreach ($request->products as $productData) {
        $order->products()->attach($productData['product_id'], [
            'quantity' => $productData['quantity'],
            'price' => $productData['price'],
        ]);
    }

    return response()->json(['message' => 'Order created successfully', 'order' => $order], 201);
}

}
