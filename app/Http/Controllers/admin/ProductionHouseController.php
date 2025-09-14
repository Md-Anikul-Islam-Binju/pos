<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateProductionHouseBalance;
use App\Models\ProductionHouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class ProductionHouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('production-house-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }

    public function index()
    {
        UpdateProductionHouseBalance::dispatch();
        $productionHouse = ProductionHouse::all();
        return view('admin.pages.production-house.index', compact('productionHouse'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'address' => 'required',
                'phone' => 'required',
            ]);
            $productionHouse = new ProductionHouse();
            $productionHouse->name = $request->name;
            $productionHouse->address = $request->address;
            $productionHouse->phone = $request->phone;
            $productionHouse->email = $request->email;
            $productionHouse->save();
            UpdateProductionHouseBalance::dispatch();
            Toastr::success('Production House Added Successfully', 'Success');
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
                'address' => 'required',
                'phone' => 'required',
                'status' => 'required',
            ]);
            $productionHouse = ProductionHouse::find($id);
            $productionHouse->name = $request->name;
            $productionHouse->address = $request->address;
            $productionHouse->phone = $request->phone;
            $productionHouse->email = $request->email;
            $productionHouse->status = $request->status;
            $productionHouse->save();
            UpdateProductionHouseBalance::dispatch();
            Toastr::success('Production House Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $productionHouse = ProductionHouse::find($id);
            $productionHouse->delete();
            UpdateProductionHouseBalance::dispatch();
            Toastr::success('Production House Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
