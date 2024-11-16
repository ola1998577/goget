<?php


namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categories = Category::with('parent','translations')->whereNull('parent_id')->latest()->get();
            return DataTables::of($categories)
                ->addIndexColumn()
                ->addColumn('parent', function ($category) {
                    return $category->parent ? $category->parent->name : 'N/A';
                })
                ->addColumn('name', function ($category) {
                    $url = route("categories.children", $category->id);
                    $categoryName = $category->translations->where('language', app()->getLocale())->first()->title ?? $category->title;
                    return '<a href="' . $url . '">' . $categoryName . '</a>';
                })
                ->addColumn('action', function ($category) {
                    $btn = '<a href="javascript:void(0)" data-id="' . $category->id . '" class="edit btn btn-info btn-sm editCategory">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $category->id . '" class="delete btn btn-danger btn-sm deleteCategory">Delete</a>';
                    return $btn;
                })
                ->addColumn('created_at', function ($category) {
                    return \Carbon\Carbon::parse($category->created_at)->format('d-m-Y H:i:s');
                })
                ->rawColumns(['action','name'])
                ->make(true);
        }

        $parentCategories = Category::whereNull('parent_id')->get();
        return view('categories.index', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|array',  // اسم الفئة بلغات مختلفة كـ Array
            'name.*' => 'string|max:255', // تحقق من صحة الاسم لكل لغة
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        $category = new Category();
        $category->parent_id = $request->parent_id;

        // if ($request->hasFile('image')) {
        //     $category->image = $request->file('image')->store('images', 'public');
        // }
        if ($request->hasFile('image')) {
            // احفظ الصورة الجديدة
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension(); // تعيين اسم فريد للصورة
            $image->move(public_path('images'), $imageName); // نقل الصورة إلى مجلد images
            $category->image = $imageName; // احفظ اسم الصورة في قاعدة البيانات
        }
        $category->save();

        // حفظ الترجمات
        foreach ($request->name as $locale => $name) {
            $category->translations()->create([
                'language' => $locale,
                'title' => $name,
            ]);
        }

        return response()->json(['success' => 'Category created successfully.']);
    }

    public function edit($id)
    {
        $category = Category::with('parent','translations')->find($id);

        if (!$category) {
            return response()->json(['error' => 'Category not found.'], 404);
        }

        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|array',
            'name.*' => 'string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        $category = Category::findOrFail($id);
        $category->parent_id = $request->parent_id;
        if ($request->hasFile('image')) {
            // احفظ الصورة الجديدة وقم بإزالة الصورة القديمة إذا كانت موجودة
            if ($category->image) {
                // قم بحذف الصورة القديمة إذا كانت موجودة
                $oldImagePath = public_path('images/' . $category->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);  // حذف الصورة القديمة
                }
            }

            // احفظ الصورة الجديدة
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension(); // تعيين اسم فريد للصورة
            $image->move(public_path('images'), $imageName); // نقل الصورة إلى مجلد images
            $category->image = $imageName; // احفظ اسم الصورة في قاعدة البيانات
        }

        $category->save();

        // تحديث الترجمات
        foreach ($request->name as $locale => $name) {
            $category->translations()->updateOrCreate(
                ['language' => $locale],
                ['title' => $name]

            );
        }

        return response()->json(['success' => 'Category updated successfully.']);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['success' => 'Category deleted successfully.']);
    }

    public function getChildrenData($id)
{            $category = Category::with('parent','translations')->where('id',$id)->first();

    // $category = Category::findOrFail($id);
    $children = $category->children;

    return DataTables::of($children)
    ->addIndexColumn()

    ->addColumn('parent', function ($category) {
        return $category->parent ? $category->parent->name : 'N/A';
    })
    ->addColumn('name', function ($category) {
        $url = route("categories.children", $category->id);
        $categoryName = $category->translations->where('language', app()->getLocale())->first()->title ?? $category->title;
        return '<a href="' . $url . '">' . $categoryName . '</a>';
    })  ->addColumn('created_at', function ($user) {
        return \Carbon\Carbon::parse($user->created_at)->format('d-m-Y H:i:s'); // اختر التنسيق الذي تريده
    })
    ->addColumn('action', function ($category) {
        $btn = '<a href="javascript:void(0)" data-id="' . $category->id . '" class="edit btn btn-info btn-sm editCategory">Edit</a>';
        $btn .= ' <a href="javascript:void(0)" data-id="' . $category->id . '" class="delete btn btn-danger btn-sm deleteCategory">Delete</a>';
        return $btn;
    })
        ->rawColumns(['action','name'])

        ->make(true);

}

public function children($id)
{
    $category = Category::find($id);

    if (!$category) {
        return redirect()->route('categories.index')->with('error', 'Category not found.');
    }

    return view('categories.children', compact('category'));
}
}

