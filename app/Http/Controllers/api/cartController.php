<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\cart;
use App\Models\product;
use App\Models\token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class cartController extends Controller
{
    //عرض جميع البيانات في السلة
    public function index(Request $request)
    {
        $token = Token::where('token', $request->header('userToken'))->first();
        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $apiKey = $request->header('api-key');
        if ($apiKey && strlen($apiKey) > 4) {
            $apiKey = substr($apiKey, 2, -2);
        }
        if ($apiKey !== $token->key) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // استلام اللغة من الترويسة (الافتراضية 'en')
        $language = $request->header('lang', 'en');

        // جلب بيانات المتاجر المرتبطة بالـ cart وحساب إجمالي العناصر والسعر لكل متجر
        $cartData = Cart::with(['store', 'product'])
            ->where('token_id', $token->id)
            ->get()
            ->groupBy('store_id')
            ->map(function ($items, $storeId) use ($language) {
                $store = $items->first()->store;
                $totalPrice = $items->sum(function ($item) {
                    return $item->product->price;
                });
                $itemCount = $items->count();

                return [
                    'store_id' => $store->id,
                    'store_name' => $store->getTranslation('name', $language), // استخدام الترجمة حسب اللغة
                    'store_image' => $store->image,
                    'item_count' => $itemCount,
                    'total_price' => $totalPrice,
                ];
            })
            ->values();

        return response()->json($cartData, 200);
    }

    public function getProductsByStore(Request $request)
    {
        $storeId = $request->storeId;
        $token = Token::where('token', $request->header('userToken'))->first();
        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $apiKey = $request->header('api-key');
        if ($apiKey && strlen($apiKey) > 4) {
            $apiKey = substr($apiKey, 2, -2);
        }
        if ($apiKey !== $token->key) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // استلام اللغة من الترويسة (الافتراضية 'en')
        $language = $request->header('lang', 'en');

        // جلب العناصر من cart المتعلقة بالمتجر المحدد
        $cartItems = Cart::with(['product.sizes', 'product.colors'])
            ->where('store_id', $storeId)
            ->where('token_id', $token->id)
            ->get();

        // تجهيز البيانات لإرجاعها
        $products = $cartItems->map(function ($cartItem) use ($language) {
            $product = $cartItem->product;

            return [
                'cart_id' => $cartItem->id,
                'product_id' => $product->id,
                'product_image' => $product->image,
                'product_name' => $product->getTranslation('title', $language), // جلب الترجمة حسب اللغة
                'product_price' => $product->price,
                'available_sizes' => $product->sizes->pluck('size'), // استخراج المقاسات
                'available_colors' => $product->colors->pluck('color'), // استخراج الألوان
            ];
        });

        return response()->json(['products' => $products], 200);
    }




    //الاضافة للسلة
    public function addToCart(Request $request)
{
    // التحقق من صلاحية المستخدم
    $token = Token::where('token', $request->header('userToken'))->first();
    if (!$token) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $apiKey = $request->header('api-key');
    if ($apiKey && strlen($apiKey) > 4) {
        $apiKey = substr($apiKey, 2, -2);
    }
    if ($apiKey !== $token->key) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }


    $validator = Validator::make($request->all(), [
        'product_id' => 'required|exists:products,id',
        'store_id' => 'required|exists:stores,id',
    ]);

    // التحقق مما إذا كانت البيانات غير صالحة
    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422); // إرسال رد يحتوي على الأخطاء
    }

    // التحقق من وجود المنتج في المتجر المطلوب
    $product = product::where('id', $request->product_id)
                      ->where('store_id', $request->store_id)
                      ->first();

    if (!$product) {
        return response()->json(['message' => 'Product not available in this store'], 404);
    }

    // التحقق مما إذا كان المنتج موجودًا بالفعل في cart لنفس المستخدم والمتجر
    $cartItem = Cart::where('product_id', $request->product_id)
                    ->where('store_id', $request->store_id)
                    ->where('token_id', $token->id)
                    ->first();

    if ($cartItem) {
        // $cartItem->update([
        //     'quantity' => $cartItem->quantity + 1,
        // ]);

        return response()->json(['message' => 'Product exists'], 401);
    }

    // إضافة المنتج إلى cart إذا لم يكن موجودًا مسبقًا
    $cartItem = Cart::create([
        'product_id' => $request->product_id,
        'store_id' => $request->store_id,
        'token_id' => $token->id,
    ]);

    return response()->json(['message' => 'Product added to cart successfully'], 200);
}


    //الحذف من السلة
    public function removeFromCart(Request $request)
    {
        // التحقق من صلاحية المستخدم
        $token = Token::where('token', $request->header('userToken'))->first();
        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $apiKey = $request->header('api-key');
        if ($apiKey && strlen($apiKey) > 4) {
            $apiKey = substr($apiKey, 2, -2);
        }
        if ($apiKey !== $token->key) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }



        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'store_id' => 'required|exists:stores,id',
        ]);

        // التحقق مما إذا كانت البيانات غير صالحة
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422); // إرسال رد يحتوي على الأخطاء
        }


        // التحقق من وجود المنتج في المتجر المطلوب داخل الـ cart للمستخدم الحالي
        $cartItem = Cart::where('product_id', $request->product_id)
                        ->where('store_id', $request->store_id)
                        ->where('token_id', $token->id)
                        ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Product not found in cart'], 404);
        }

        // حذف العنصر من cart
        $cartItem->delete();

        return response()->json(['message' => 'Product removed from cart successfully'], 200);
    }


    //تعديل كمية المنتج في السلة
    public function updateAmount(Request $request){

    }
}
