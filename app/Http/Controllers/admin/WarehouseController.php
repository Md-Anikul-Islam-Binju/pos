<?php

namespace App\Http\Controllers\admin;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('warehouse-list')) {
                return redirect()->route('unauthorized.action');
            }
            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $warehouse = Warehouse::all();
        return view('admin.pages.warehouse.index', compact('warehouse'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|unique:warehouses,name',
                'address' => 'nullable|string',
                'phone' => 'nullable|string',
                'email' => 'nullable|email',
            ]);

            $warehouse = new Warehouse();
            $warehouse->name = $request->name;
            $warehouse->address = $request->address;
            $warehouse->phone = $request->phone;
            $warehouse->email = $request->email;
            $warehouse->save();
            Toastr::success('Warehouse Added Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            // Handle the exception here
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {

        try {
            $request->validate([
                'name' => 'required|unique:warehouses,name',
                'address' => 'required',
                'phone' => 'required',
                'email' => 'required',
                'status' => 'required',
            ]);
            $warehouse = Warehouse::find($id);
            $warehouse->name = $request->name;
            $warehouse->address = $request->address;
            $warehouse->phone = $request->phone;
            $warehouse->email = $request->email;
            $warehouse->status = $request->status;
            $warehouse->save();
            Toastr::success('Warehouse Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $warehouse = Warehouse::find($id);
            $warehouse->delete();
            Toastr::success('Warehouse Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

}
