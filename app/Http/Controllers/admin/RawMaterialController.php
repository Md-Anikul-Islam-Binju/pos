<?php

namespace App\Http\Controllers\admin;

use App\Models\Size;
use App\Models\Unit;
use App\Models\Brand;
use App\Models\Color;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RawMaterialCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class RawMaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('brand-list')) {
                return redirect()->route('unauthorized.action');
            }
            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $material = RawMaterial::all();
        $category = RawMaterialCategory::all();
        return view('admin.pages.raw-material.index', compact('material', 'category'));
    }

    public function create()
    {
        $category = RawMaterialCategory::all();
        $unit = RawMaterialCategory::all();
        $brand = RawMaterialCategory::all();
        $size = RawMaterialCategory::all();
        $color = RawMaterialCategory::all();
        return view('admin.pages.raw-material.create', compact('category', 'unit', 'brand', 'size', 'color'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'raw_material_category_id' => 'required',
                'sku' => 'required|unique:raw_materials,sku',
                'size_id' => 'nullable|array',
                'size_id.*' => 'exists:sizes,id',
                'color_id' => 'nullable|array',
                'color_id.*' => 'exists:colors,id',
                'brand_id' => 'nullable|array',
                'brand_id.*' => 'exists:brands,id',
            ]);

            $rawMaterial = new RawMaterial();
            $rawMaterial->name = $request->name;
            $rawMaterial->raw_material_category_id = $request->raw_material_category_id;
            $rawMaterial->sku = $request->sku;
            $rawMaterial->width = $request->width;
            $rawMaterial->length = $request->length;
            $rawMaterial->density = $request->density;
            $rawMaterial->unit_id = $request->unit_id;
            $rawMaterial->save();

            if ($request->filled('size_id')) {
                $rawMaterial->sizes()->sync($request->size_id);
            }

            if ($request->filled('color_id')) {
                $rawMaterial->colors()->sync($request->color_id);
            }

            if ($request->filled('brand_id')) {
                $rawMaterial->brands()->sync($request->brand_id);
            }

            Toastr::success('Raw Material Added Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            // Handle the exception here
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $rawMaterial = RawMaterial::find($id);
        $category = RawMaterialCategory::all();
        $unit = Unit::all();
        $brand = Brand::all();
        $size = Size::all();
        $color = Color::all();
        $brand_id = DB::table('brand_raw_material')->where('raw_material_id', $id)->pluck('brand_id');
        $size_id = DB::table('size_raw_material')->where('raw_material_id', $id)->pluck('size_id');
        $color_id = DB::table('color_raw_material')->where('raw_material_id', $id)->pluck('color_id');
        return view('admin.pages.raw-material.edit', compact('rawMaterial', 'category', 'unit', 'brand', 'size', 'color', 'brand_id', 'size_id', 'color_id'));
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required',
                'raw_material_category_id' => 'required',
                'sku' => 'required|unique:raw_materials,sku,' . $id,
                'size_id' => 'nullable|array',
                'size_id.*' => 'exists:sizes,id',
                'color_id' => 'nullable|array',
                'color_id.*' => 'exists:colors,id',
                'brand_id' => 'nullable|array',
                'brand_id.*' => 'exists:brands,id',
            ]);

            $rawMaterial = RawMaterial::find($id);
            $rawMaterial->name = $request->name;
            $rawMaterial->raw_material_category_id = $request->raw_material_category_id;
            $rawMaterial->sku = $request->sku;
            $rawMaterial->width = $request->width;
            $rawMaterial->length = $request->length;
            $rawMaterial->density = $request->density;
            $rawMaterial->unit_id = $request->unit_id;
            $rawMaterial->save();

            if ($request->filled('size_id')) {
                $rawMaterial->sizes()->sync($request->size_id);
            } else {
                $rawMaterial->sizes()->detach();
            }

            if ($request->filled('color_id')) {
                $rawMaterial->colors()->sync($request->color_id);
            } else {
                $rawMaterial->colors()->detach();
            }

            if ($request->filled('brand_id')) {
                $rawMaterial->brands()->sync($request->brand_id);
            } else {
                $rawMaterial->brands()->detach();
            }

            Toastr::success('Brand Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $rawMaterial = RawMaterial::findOrFail($id);
        return view('admin.pages.raw-material.show', compact('rawMaterial'));
    }

    public function destroy($id)
    {
        try {
            $rawMaterial = RawMaterial::find($id);
            $rawMaterial->delete();
            Toastr::success('Raw Material Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function getAllMaterials()
    {
        $materials = RawMaterial::with(['brands','colors','sizes', 'unit'])->orderBy('id', 'DESC')->get();
        return response()->json($materials);
    }
}
