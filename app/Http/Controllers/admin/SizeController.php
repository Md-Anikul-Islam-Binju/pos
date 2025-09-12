<?php

namespace App\Http\Controllers\admin;

use App\Models\Size;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class SizeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('size-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $size = Size::all();
        return view('admin.pages.size.index', compact('size'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
            ]);
            $size = new Size();
            $size->name = $request->name;
            $size->save();
            Toastr::success('Size Added Successfully', 'Success');
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
                'status' => 'required',
            ]);
            $size = Size::find($id);
            $size->name = $request->name;
            $size->status = $request->status;
            $size->save();
            Toastr::success('Size Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $size = Size::find($id);
            $size->delete();
            Toastr::success('Size Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
