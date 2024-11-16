<?php

// app/Http/Controllers/ProductController.php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\product_image;
use App\Models\Store;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $language = app()->getLocale(); // استرجاع اللغة الحالية (مثل 'en' أو 'ar')

            $products = Product::with([
                'category.translations' => function ($query) use ($language) {
                    $query->where('language', $language);
                },
                'store.translations' => function ($query) use ($language) {
                    $query->where('language', $language);
                },
                'translations' => function ($query) use ($language) {
                    $query->where('language', $language);
                }
            ])->select('products.*');
            return datatables()->of($products)
                ->addIndexColumn()
                ->addColumn('name', function ($product) {
                    $imageUrl = $product->image ? asset('images/products/' . $product->image) : asset('images/products/default-image.jpg');

                    $productName = $product->translations->first()->title;
                    return '<h2 class="table-avatar">
                            <a href="profile.html" class="avatar"><img alt="" src="' . asset($imageUrl) . '"></a>
                            <a href="#">' . $productName . '</a>
                        </h2>';
                })
                ->addColumn('category', function ($product) {
                    return $product->category->translations->first()->title ?? '-';
                })
                ->addColumn('store', function ($product) {
                    return $product->store->translations->first()->name ?? '-';
                })
                ->addColumn('price', function ($product) {
                    return '$' . number_format($product->price, 2);
                })
                ->addColumn('quantity', function ($product) {
                    return $product->amount > 0 ? $product->amount : '<span class="text-danger">Out of Stock</span>';
                })
                ->addColumn('colors_sizes', function ($product) {
                    $colors = $product->colors->count();
                    $sizes = $product->sizes->count();
                    return "{$colors} Colors, {$sizes} Sizes";
                })
                ->addColumn('action', function ($product) {
                    $editUrl = route('products.edit', $product->id);
                    $showUrl = route('products.show', $product->id);
                    $btn = '<a href="' . $editUrl . '" class=" btn btn-info btn-sm " style="margin-inline-end: 5px;"> <i class="fa fa-edit"></i> </a>';
                    $btn .= '<a href="' . $showUrl . '" class="btn btn-secondary btn-sm"> <i class="fa fa-eye"></i> </a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $product->id . '" data-original-title="Delete" class="btn btn-danger btn-sm delete"> <i class="fa fa-trash-o"></i> </a>';

                    return $btn;
                })
                ->rawColumns(['name', 'quantity', 'action'])
                ->make(true);
        }

        return view('products.index');
    }


    public function create()
    {
        // جلب التصنيفات والمخازن لاختيارها في الفورم
        $categories = Category::all();
        $stores = Store::all();

        return view('products.create', compact('categories', 'stores'));
    }

    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'store_id' => 'required|exists:stores,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'amount' => 'required|integer|min:0',
            'type' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'language' => 'required|string|size:2',
            'additional_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'colors.*' => 'string|max:50',
            'sizes.*' => 'string|max:50',
        ]);

        // حساب السعر بعد الخصم
        $totalPrice = $request->price - ($request->price * ($request->discount / 100));
        if ($request->hasFile('image')) {
            // احفظ الصورة الجديدة
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension(); // تعيين اسم فريد للصورة
            $image->move(public_path('images/products'), $imageName); // نقل الصورة إلى مجلد images
            ; // احفظ اسم الصورة في قاعدة البيانات
        }
        // حفظ المنتج الأساسي
        $product = Product::create([
            'category_id' => $request->category_id,
            'store_id' => $request->store_id,
            'image' => $imageName,
            'price' => $request->price,
            'discount' => $request->discount,
            'total_price' => $totalPrice,
            'amount' => $request->amount,
            'type' => $request->type,
        ]);

        // حفظ الترجمة
        $product->translations()->create([
            'title' => $request->title,
            'description' => $request->description,
            'language' => $request->language,
        ]);

        // حفظ الصور الإضافية
        if ($request->has('additional_images')) {
            foreach ($request->file('additional_images') as $image) {
                $imageName = time() . '-' . $image->getClientOriginalName(); // اسم فريد لكل صورة
                $image->move(public_path('images/products'), $imageName); // نقل الصورة إلى public_path
                $product->images()->create([
                    'image' => $imageName, // حفظ اسم الصورة في قاعدة البيانات
                ]);
            }
        }

        // حفظ الألوان
        if ($request->has('colors')) {
            foreach ($request->colors as $color) {
                $product->colors()->create(['color' => $color]);
            }
        }

        // حفظ الأحجام
        if ($request->has('sizes')) {
            foreach ($request->sizes as $size) {
                $product->sizes()->create(['size' => $size]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Product added successfully!');
    }

    public function edit($id)
    {
        $language = app()->getLocale(); // استرجاع اللغة الحالية (مثل 'en' أو 'ar')

        $product = Product::with([
            'category.translations' => function ($query) use ($language) {
                $query->where('language', $language);
            },
            'store.translations' => function ($query) use ($language) {
                $query->where('language', $language);
            },
            'translations' => function ($query) use ($language) {
                $query->where('language', $language);
            }
        ])->findOrFail($id);
        $categories = Category::all();
        $stores = Store::all();

        return view('products.edit', compact('product', 'categories', 'stores'));
    }

    public function show($id)
    {
        $language = app()->getLocale(); // استرجاع اللغة الحالية (مثل 'en' أو 'ar')

        $product = Product::with([
            'category.translations' => function ($query) use ($language) {
                $query->where('language', $language);
            },
            'store.translations' => function ($query) use ($language) {
                $query->where('language', $language);
            },
            'translations' => function ($query) use ($language) {
                $query->where('language', $language);
            }
        ])->findOrFail($id);        $categories = Category::all();
        $stores = Store::all();

        return view('products.show', compact('product', 'categories', 'stores'));
    }

    public function update(Request $request, $id)
    {
        // العثور على المنتج باستخدام الـ id
        $product = Product::findOrFail($id);

        // التحقق من صحة البيانات
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'store_id' => 'required|exists:stores,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'amount' => 'required|integer|min:0',
            'type' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'language' => 'required|string|size:2',
            'additional_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'colors.*' => 'string|max:50',
            'sizes.*' => 'string|max:50',
        ]);

        // حساب السعر بعد الخصم
        $totalPrice = $request->price - ($request->price * ($request->discount / 100));

        // تحديث بيانات المنتج الأساسي
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($product->image && file_exists(public_path('images/products/' . $product->image))) {
                unlink(public_path('images/products/' . $product->image));
            }

            // حفظ الصورة الجديدة
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/products'), $imageName);
            $product->image = $imageName;
        }

        $product->update([
            'category_id' => $request->category_id,
            'store_id' => $request->store_id,
            'price' => $request->price,
            'discount' => $request->discount,
            'total_price' => $totalPrice,
            'amount' => $request->amount,
            'type' => $request->type,
        ]);

        // تحديث الترجمة
        $translation = $product->translations()->where('language', $request->language)->first();
        if ($translation) {
            $translation->update([
                'title' => $request->title,
                'description' => $request->description,
            ]);
        } else {
            $product->translations()->create([
                'title' => $request->title,
                'description' => $request->description,
                'language' => $request->language,
            ]);
        }


        // إضافة الصور الإضافية الجديدة
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $image) {
                $imageName = time() . '-' . $image->getClientOriginalName();
                $image->move(public_path('images/products'), $imageName);
                $product->images()->create([
                    'image' => $imageName,
                ]);
            }
        }


        // تحديث الألوان
        $product->colors()->delete(); // حذف الألوان القديمة
        if ($request->has('colors')) {
            foreach ($request->colors as $color) {
                $product->colors()->create(['color' => $color]);
            }
        }

        // تحديث الأحجام
        $product->sizes()->delete(); // حذف الأحجام القديمة
        if ($request->has('sizes')) {
            foreach ($request->sizes as $size) {
                $product->sizes()->create(['size' => $size]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }


    public function destroy($id)
    {
        $category = Product::findOrFail($id);
        $category->delete();

        return response()->json(['success' => 'Category deleted successfully.']);
    }

    public function deleteImage($id)
{
    $image = product_image::findOrFail($id); // جلب الصورة
    $imagePath = public_path('images/products/' . $image->image);

    // حذف الملف من السيرفر
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }

    // حذف السجل من قاعدة البيانات
    $image->delete();

    // return back()->with('success', 'Image deleted successfully!');
    return response()->json(['success' => 'Image deleted successfully!']);

}

}
