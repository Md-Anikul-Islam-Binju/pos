<?php

namespace App\Http\Controllers\admin;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Jobs\UpdateSupplierBalance;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('supplier-list')) {
                return redirect()->route('unauthorized.action');
            }
            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $supplier = Supplier::all();
        return view('admin.pages.supplier.index', compact('supplier'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'phone' => 'required',
                'email' => 'required',
                'contact_person' => 'required',
                'address' => 'required',
            ]);

            UpdateSupplierBalance::dispatch();

            $supplier = new Supplier();
            $supplier->name = $request->name;
            $supplier->phone = $request->phone;
            $supplier->email = $request->email;
            $supplier->contact_person = $request->contact_person;
            $supplier->address = $request->address;
            $supplier->save();
            Toastr::success('Supplier Added Successfully', 'Success');
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
                'name' => 'required',
                'phone' => 'required',
                'email' => 'required',
                'contact_person' => 'required',
                'address' => 'required',
            ]);
            $supplier = Supplier::find($id);
            $$supplier->name = $request->name;
            $supplier->phone = $request->phone;
            $supplier->email = $request->email;
            $supplier->contact_person = $request->contact_person;
            $supplier->address = $request->address;
            $supplier->save();

            UpdateSupplierBalance::dispatch();

            Toastr::success('Supplier Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $supplier = Supplier::find($id);
            $supplier->delete();
            Toastr::success('Supplier Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
