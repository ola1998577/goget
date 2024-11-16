<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\banner;
use App\Models\cart;
use App\Models\category;
use App\Models\favourite;
use App\Models\product;
use App\Models\quiz;
use App\Models\store;
use App\Models\token;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Ui\Presets\React;
use Carbon\Carbon;


class homeController extends Controller
{
    public function home(Request $request)
    {
        if ($request->header('userToken')) {
            $language = $request->header('lang', 'en'); // القيمة الافتراضية هي 'en'

            $token = token::where('token', $request->header('userToken'))->first();
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

            if ($token->quiz_today == 0) {
                $today = Carbon::today();
                $quiz = quiz::whereDate('created_at', $today)->first();
            }

            // جلب الصور من البنرات
            $banners = banner::select('image')->get();

            // جلب الفئات حسب اللغة
            $categories = category::where('parent_id', null)
                ->with(['translations' => function ($query) use ($language) {
                    $query->where('language', $language)->select('category_id', 'title as name');
                }])
                ->select('id', 'image')
                ->get()
                ->map(function ($category) {
                    $category->name = $category->translations->isNotEmpty() ? $category->translations->first()->name : null;
                    unset($category->translations);
                    return $category;
                });

            // جلب المتاجر
            $stores = store::select('id', 'image')->get();

            // جلب المنتجات مع الترجمة المطلوبة والتحقق من المفضلة وعربة التسوق حسب token_id
            $products = product::with([
                'store:id,image',
                'store.translations' => function ($query) use ($language) {
                    $query->where('language', $language)->select('store_id', 'name');
                },
                'category:id,image',
                'category.translations' => function ($query) use ($language) {
                    $query->where('language', $language)->select('category_id', 'title as name');
                },
                'images:id,product_id,image',
                'translations' => function ($query) use ($language) {
                    $query->where('language', $language)->select('product_id', 'title as name', 'description');
                }
            ])
                ->select('id', 'price', 'store_id', 'category_id') // تأكد من جلب store_id و category_id
                ->limit(6)
                ->get()
                ->map(function ($product) use ($token) {
                    $product->name = $product->translations->isNotEmpty() ? $product->translations->first()->name : null;

                    // جلب اسم التصنيف بناءً على الترجمة
                    if ($product->category && $product->category->translations->isNotEmpty()) {
                        $product->category->name = $product->category->translations->first()->name;
                        unset($product->category->translations);
                    }

                    // جلب اسم المتجر بناءً على الترجمة
                    if ($product->store && $product->store->translations->isNotEmpty()) {
                        $product->store->name = $product->store->translations->first()->name;
                        unset($product->store->translations);
                    }

                    // التحقق من المفضلة والعربة
                    $product->is_favorite = favourite::where('product_id', $product->id)->where('token_id', $token->id)->exists();
                    $product->in_cart = cart::where('product_id', $product->id)->where('token_id', $token->id)->exists();

                    unset($product->translations);
                    return $product;
                });


            // إعداد الاستجابة النهائية
            $all = [
                'banners' => $banners,
                'categories' => $categories,
                'stores' => $stores,
                'products' => $products,
                'quiz' => $quiz ?? null,
            ];

            return response()->json($all, 200);
        }
    }


    public function products(Request $request)
    {
        if ($request->header('userToken')) {
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

            $language = $request->header('lang', 'en');

            if ($request->type == 'product') {
                // إعداد الاستعلام للمنتجات
                $productsQuery = Product::with([
                    'store:id,image',
                    'store.translations' => function ($query) use ($language) {
                        $query->where('language', $language)->select('store_id', 'name');
                    },
                    'category:id,image',
                    'category.translations' => function ($query) use ($language) {
                        $query->where('language', $language)->select('category_id', 'title as name');
                    },
                    'images:id,product_id,image',
                    'translations' => function ($query) use ($language) {
                        $query->where('language', $language)->select('product_id', 'title as name', 'description');
                    }
                ])
                    ->select('id', 'price', 'store_id', 'category_id');

                // تطبيق الفلاتر بناءً على المدخلات من المستخدم
                if ($request->filled('category_id')) {
                    $productsQuery->where('category_id', $request->category_id);
                }

                if ($request->filled('size')) {
                    $productsQuery->whereHas('size', function ($query) use ($request) {
                        $query->where('size', $request->size);
                    });
                }

                if ($request->filled('color')) {
                    $productsQuery->whereHas('color', function ($query) use ($request) {
                        $query->where('color', $request->color);
                    });
                }

                if ($request->filled('min_price') && $request->filled('max_price')) {
                    $productsQuery->whereBetween('price', [$request->min_price, $request->max_price]);
                } elseif ($request->filled('min_price')) {
                    $productsQuery->where('price', '>=', $request->min_price);
                } elseif ($request->filled('max_price')) {
                    $productsQuery->where('price', '<=', $request->max_price);
                }

                if ($request->filled('popular')) {
                    $productsQuery->where('is_popular', $request->popular);
                }

                if ($request->filled('order_by')) {
                    if ($request->order_by == 'latest') {
                        $productsQuery->orderBy('created_at', 'desc');
                    } elseif ($request->order_by == 'price_asc') {
                        $productsQuery->orderBy('price', 'asc');
                    } elseif ($request->order_by == 'price_desc') {
                        $productsQuery->orderBy('price', 'desc');
                    }
                }
                $products = $productsQuery->paginate(1);

                // إعادة المعالجة على كل منتج وكل عرض للحصول على الاسم والترجمات
                $products->getCollection()->transform(function ($product) use ($token) {
                    $product->name = $product->translations->isNotEmpty() ? $product->translations->first()->name : null;

                    if ($product->category && $product->category->translations->isNotEmpty()) {
                        $product->category->name = $product->category->translations->first()->name;
                        unset($product->category->translations);
                    }

                    if ($product->store && $product->store->translations->isNotEmpty()) {
                        $product->store->name = $product->store->translations->first()->name;
                        unset($product->store->translations);
                    }

                    $product->is_favorite = favourite::where('product_id', $product->id)->where('token_id', $token->id)->exists();
                    $product->in_cart = cart::where('product_id', $product->id)->where('token_id', $token->id)->exists();

                    unset($product->translations);

                    return $product;
                });
                return response()->json([
                    'products' => $products
                ], 200);
            } elseif ($request->type == 'offer') {
                $offersQuery = Product::with([
                    'store:id,image',
                    'store.translations' => function ($query) use ($language) {
                        $query->where('language', $language)->select('store_id', 'name');
                    },
                    'category:id,image',
                    'category.translations' => function ($query) use ($language) {
                        $query->where('language', $language)->select('category_id', 'title as name');
                    },
                    'images:id,product_id,image',
                    'translations' => function ($query) use ($language) {
                        $query->where('language', $language)->select('product_id', 'title as name', 'description');
                    }
                ])
                    ->whereNotNull('discount')
                    ->select('id', 'price', 'discount', 'store_id', 'category_id');

                // تطبيق نفس فلاتر `product` على `offers`
                if ($request->filled('order_by')) {
                    if ($request->order_by == 'latest') {
                        $offersQuery->orderBy('created_at', 'desc');
                    } elseif ($request->order_by == 'price_asc') {
                        $offersQuery->orderBy('price', 'asc');
                    } elseif ($request->order_by == 'price_desc') {
                        $offersQuery->orderBy('price', 'desc');
                    }
                }
                $offers = $offersQuery->paginate(6);

                $offers->getCollection()->transform(function ($offer) use ($token) {
                    $offer->name = $offer->translations->isNotEmpty() ? $offer->translations->first()->name : null;

                    if ($offer->category && $offer->category->translations->isNotEmpty()) {
                        $offer->category->name = $offer->category->translations->first()->name;
                        unset($offer->category->translations);
                    }

                    if ($offer->store && $offer->store->translations->isNotEmpty()) {
                        $offer->store->name = $offer->store->translations->first()->name;
                        unset($offer->store->translations);
                    }

                    $offer->is_favorite = favourite::where('product_id', $offer->id)->where('token_id', $token->id)->exists();
                    $offer->in_cart = cart::where('product_id', $offer->id)->where('token_id', $token->id)->exists();

                    unset($offer->translations);

                    return $offer;
                });
                return response()->json([
                    'offers' => $offers
                ], 200);
            } else {
                // إعداد الاستعلام للـ stores
                // إعداد الاستعلام للـ stores
                $storesQuery = Store::with(['translations' => function ($query) use ($language) {
                    $query->where('language', $language)->select('store_id', 'name');
                }])
                    ->select('id', 'image');
                $stores = $storesQuery->paginate(6);

                $stores->getCollection()->transform(function ($store) {
                    $store->name = $store->translations->isNotEmpty() ? $store->translations->first()->name : null;
                    unset($store->translations);

                    return $store;
                });
                return response()->json([
                    'stores' => $stores
                ], 200);
            }
        }
    }

    public function all_store(Request $request)
    {
        if ($request->header('userToken')) {
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

            $language = $request->header('lang', 'en');

            // استعلام لجلب المتاجر وترجمتها وعد المنتجات
            $stores = Store::withCount('product')
                ->with(['translations' => function ($query) use ($language) {
                    $query->where('language', $language)->select('store_id', 'name');
                }])
                ->get()
                ->map(function ($store) {
                    // التحقق من وجود ترجمة
                    $name = optional($store->translations->first())->name;

                    // إرجاع البيانات المطلوبة فقط
                    return [
                        'id' => $store->id,
                        'image' => $store->image,
                        'product_count' => $store->product_count ?? 0,  // تأكيد تعيين قيمة product_count إذا كانت العلاقة موجودة
                        'name' => $name,
                    ];
                });

            return response()->json($stores, 200);
        }
    }

    public function all_category(Request $request)
    {
        if ($request->header('userToken')) {
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

            // استعلام لجلب الفئات وترجمتها بناءً على اللغة وعدد المتاجر
            $categories = Category::withCount('product')  // إضافة علاقة مع stores لاحتساب عدد المتاجر
                ->with(['translations' => function ($query) use ($language) {
                    $query->where('language', $language)->select('category_id', 'title as name');
                }])->get();

            // معالجة الترجمة لكل فئة
            $categories->transform(function ($category) {
                $category->name = $category->translations->isNotEmpty() ? $category->translations->first()->name : $category->name;
                unset($category->translations);
                unset($category->stores_count); // إزالة العنصر المساعد
                return $category;
            });

            return response()->json($categories, 200);
        }
    }

    public function show(Request $request)
    {
        $id = $request->id;

        // التحقق من الـ apiKey و userToken
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

        // استعلام لجلب تفاصيل المنتج مع الترجمات
        $product = Product::with([
            'images',           // الصور
            'reviews',          // المراجعات
            'colors',           // الألوان
            'sizes',            // الأحجام
            'category',         // التصنيف
            'store',            // المتجر
            'translations' => function ($query) use ($language) {
                // استرجاع الترجمة بلغة معينة (مثل en أو ar)
                $query->where('language', $language);
            }
        ])->find($id);

        // إذا لم يتم العثور على المنتج
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // إضافة الترجمة (العنوان والوصف) للمنتج
        $translation = $product->translations->first(); // الحصول على الترجمة الأولى بناءً على اللغة
        $product->title = $translation ? $translation->title : null;
        $product->description = $translation ? $translation->description : null;

        // إزالة الترجمة من النتائج (عدم إرجاع `translations` كحقل منفصل)
        unset($product->translations);

        // التحقق من وجود المنتج في السلة أو المفضلة للمستخدم بناءً على token
        $product->is_favorite = Favourite::where('product_id', $product->id)->where('token_id', $token->id)->exists();
        $product->in_cart = Cart::where('product_id', $product->id)->where('token_id', $token->id)->exists();

        // إضافة المنتجات المشابهة بناءً على التصنيف (يمكنك تعديل المعايير)
        $similarProducts = Product::with([
            'translations' => function ($query) use ($language) {
                // استرجاع الترجمة بناءً على اللغة المرسلة
                $query->where('language', $language);
            }
        ])
            ->where('category_id', $product->category_id)  // جلب منتجات من نفس التصنيف
            ->where('id', '!=', $product->id)  // استبعاد المنتج نفسه
            ->take(6)  // جلب 6 منتجات فقط
            ->get();


        // اختيار الحقول المطلوبة فقط للمنتجات المشابهة
        // في جزء استعلام المنتجات المشابهة
        $similarProducts = $similarProducts->map(function ($similarProduct) {
            $translation = $similarProduct->translations->first();
            return [
                'id' => $similarProduct->id,
                'title' => $translation ? $translation->title : null,
                'image' => $similarProduct->image  // تعديل هنا للتحقق
            ];
        });

        // إضافة المنتجات المشابهة إلى المنتج الحالي
        $product->similar_products = $similarProducts;

        // إرجاع تفاصيل المنتج مع المنتجات المشابهة
        return response()->json($product, 200);
    }
}
