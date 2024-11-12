<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Traits\allTrait;

class UserController extends Controller
{
    use allTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // إذا كان الطلب AJAX، سنعيد بيانات JSON
        if ($request->ajax()) {
            $role = $request->get('role'); // الحصول على قيمة role من الطلب

            $users = User::with('roles')
            ->select(['id', 'f_name', 'l_name', 'email', 'created_at']);

        // إذا كانت قيمة الدور موجودة، نقوم بتصفية المستخدمين بناءً على هذا الدور
        if ($role) {
            $users = $users->whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role); // تصفية بناءً على اسم الدور
            });
        }
            return DataTables::of($users)
            ->addIndexColumn()

                ->addColumn('full_name', function ($user) {
                    return $user->f_name . $user->l_name;
                })
                ->addColumn('orders', function ($user) {
                    return $user->orders()->count();
                })
                ->addColumn('point', function ($user) {
                    return $user->point ?? 0;
                })
                ->addColumn('created_at', function ($user) {
                    return \Carbon\Carbon::parse($user->created_at)->format('d-m-Y H:i:s'); // اختر التنسيق الذي تريده
                })
                ->addColumn('action', function($row) {
                    $btn='';
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm edit"> <i class="fa fa-edit"></i> </a>';
                $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm delete"> <i class="fa fa-trash-o"></i> </a>';

                    return $btn;

                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // غير AJAX - إعادة عرض الصفحة الرئيسية مع الأدوار
        $roles = Role::all();
        return view('users.index', compact('roles'));
    }

    public function create(Request $request){
        $roles = Role::all();
        return view('users.create',compact('roles'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $roles = Role::all()->pluck('name')->toArray(); // الحصول على أسماء الأدوار المتاحة

        $request->validate([
            'f_name' => 'required|string|max:255',
            'l_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'password' => 'required|string|min:6',
            'role' => 'required|in:' . implode(',', $roles), // تحقق من كونها ضمن الأدوار المتاحة

        ]);

        $user = new User();
        $user->f_name = $request->f_name;
        $user->l_name = $request->l_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = bcrypt($request->password);
        $user->save();

        // تعيين الدور
        $user->assignRole($request->role);

        return response()->json(['success' => true, 'user' => $user]);
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $user->role = $user->roles->first()->name ?? ''; // استرجاع الدور الأول إن وجد
        return response()->json($user);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'f_name' => 'required|string|max:255',
            'l_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|unique:users,phone,' . $id,
            'role' => 'required|exists:roles,name', // تحقق من صحة الدور
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // تحديث الدور
        $user->syncRoles([$request->role]);

        return response()->json(['success' => 'User updated successfully']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Search functionality (optional).
     */
    public function search(Request $request)
    {
        // يمكنك إضافة وظيفة البحث هنا حسب الطلب.
    }
}

