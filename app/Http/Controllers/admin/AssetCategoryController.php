<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AssetCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('asset-category-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }
    public function index()
    {
        $assetCategory = AssetCategory::all();
        return view('admin.pages.asset-category.index', compact('assetCategory'));
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|unique:asset_categories,name',
            ]);

            $assetCategory = new AssetCategory();
            $assetCategory->name = $request->name;
            $assetCategory->save();
            return redirect()->back()->with('success', 'Asset Category Added Successfully');
        } catch (\Exception $e) {
            // Handle the exception here
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {

        try {
            $request->validate([
                'name' => 'required|unique:asset_categories,name,' . $id,
            ]);
            $assetCategory = AssetCategory::findOrFail($id);
            $assetCategory->name = $request->name;
            $assetCategory->status = $request->status;
            $assetCategory->save();
            return redirect()->back()->with('success', 'Asset Category Updated Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $assetCategory = AssetCategory::findOrFail($id);
            $assetCategory->delete();
            return redirect()->back()->with('success', 'Asset Category Deleted Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
