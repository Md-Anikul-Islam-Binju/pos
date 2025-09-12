<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\RawMaterialCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class RawMaterialCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('material-category-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $category = RawMaterialCategory::all();
        return view('admin.pages.raw-material-category.index', compact('category'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
            ]);

            $category = new RawMaterialCategory();
            $category->name = $request->name;
            $category->save();

            Toastr::success('Category Added Successfully', 'Success');
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

            $category = RawMaterialCategory::find($id);
            $category->name = $request->name;
            $category->status = $request->status;
            $category->save();

            Toastr::success('Category Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $category = RawMaterialCategory::find($id);
            $category->delete();

            Toastr::success('Category Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
