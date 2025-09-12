<?php

namespace App\Http\Controllers\admin;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('unit-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $unit = Unit::all();
        return view('admin.pages.unit.index', compact('unit'));
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required',
                'name' => 'required'
            ]);

            $unit = new Unit();
            $unit->name = $request->name;
            $unit->code = $request->code;
            $unit->save();
            Toastr::success('Unit Added Successfully', 'Success');
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
                'code' => 'required',
                'status' => 'required',
            ]);
            $unit = Unit::find($id);
            $unit->name = $request->name;
            $unit->code = $request->code;
            $unit->status = $request->status;
            $unit->save();
            Toastr::success('Unit Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $unit = Unit::find($id);
            $unit->delete();
            Toastr::success('Unit Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
