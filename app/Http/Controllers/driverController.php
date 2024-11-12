<?php

namespace App\Http\Controllers;

use App\Http\Traits\allTrait;
use App\Models\driver;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class driverController extends Controller
{
    use allTrait;

    public function index(Request $request)
    {
        // إذا كان الطلب AJAX، سنعيد بيانات JSON
        if ($request->ajax()) {

            // تحميل السجلات مع العلاقة delivery_company
            $users = driver::with(['delivery_company' => function ($query) {
                $query->select('id', 'f_name');
            }])->select(['id', 'name', 'phone', 'deliveryCompany_id', 'created_at']);

            return DataTables::of($users)
                ->addIndexColumn()

                ->addColumn('name', function ($user) {
                    return $user->name;
                })
                ->addColumn('phone', function ($user) {
                    return $user->phone;
                })

                ->addColumn('company', function ($user) {
                    // التأكد من أن العلاقة ليست فارغة قبل الوصول إلى f_name
                    return $user->delivery_company ? $user->delivery_company->f_name : 'N/A';
                })
                ->addColumn('created_at', function ($user) {
                    return \Carbon\Carbon::parse($user->created_at)->format('d-m-Y H:i:s'); // اختر التنسيق الذي تريده
                })
                ->addColumn('action', function($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm edit"> <i class="fa fa-edit"></i> </a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm delete"> <i class="fa fa-trash-o"></i> </a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // غير AJAX - إعادة عرض الصفحة الرئيسية مع الأدوار
        $companies = User::whereHas('roles', function ($query) {
            $query->where('name', 'company'); // تصفية بناءً على اسم الدور
        })->get();

        return view('driver.index', compact('companies'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|unique:users,phone',
            'deliveryCompany_id' => 'required|integer',
        ]);

        // التحقق من أن `deliveryCompany_id` يشير إلى مستخدم له دور "company"
        $company = User::where('id', $request->deliveryCompany_id)->first();

        if (!$company) {
            return response()->json(['error' => 'Invalid delivery company ID.'], 422);
        }

        $driver = new Driver();
        $driver->name = $request->name;
        $driver->phone = $request->phone;
        $driver->deliveryCompany_id = $request->deliveryCompany_id;
        $driver->save();

        return response()->json(['success' => true, 'driver' => $driver]);
    }


    public function edit($id)
    {
        $user = driver::with('delivery_company')->findOrFail($id);
        $user->delivery_company = $user->delivery_company->first()->f_name ?? ''; // استرجاع الدور الأول إن وجد
        return response()->json($user);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|unique:users,phone',
            'deliveryCompany_id' => 'nullable|integer',
        ]);

        $user = driver::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'deliveryCompany_id'=>$request->deliveryCompany_id
        ]);


        return response()->json(['success' => 'Driver updated successfully']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = driver::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true]);
    }
}
