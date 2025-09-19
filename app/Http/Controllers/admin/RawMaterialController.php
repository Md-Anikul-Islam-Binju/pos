<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminActivity;
use App\Models\Brand;
use App\Models\Color;
use App\Models\RawMaterial;
use App\Models\RawMaterialCategory;
use App\Models\Size;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use \Illuminate\Http\JsonResponse;

class RawMaterialController extends Controller
{
    public function index(): View|Factory|Application
    {
        $materials = RawMaterial::orderBy('id', 'DESC')->latest()->get();
        $categories = RawMaterialCategory::orderBy('id', 'DESC')->latest()->get();
        return view('admin.pages.raw-material.index', compact('materials', 'categories'));
    }

    public function create(): View|Factory|Application
    {
        $categories = RawMaterialCategory::orderBy('id', 'DESC')->get();
        $units = Unit::orderby('id', 'DESC')->get();
        $brands = Brand::orderby('id', 'DESC')->get();
        $sizes = Size::orderby('id', 'DESC')->get();
        $colors = Color::orderby('id', 'DESC')->get();
        return view('admin.pages.raw-material.create', compact('categories', 'units', 'brands', 'sizes', 'colors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'raw_material_category_id' => 'required',
            'sku' => 'required|unique:raw_materials,sku',
            'photo' => 'nullable|image',
            'size_id' => 'nullable|array',
            'size_id.*' => 'exists:sizes,id',
            'color_id' => 'nullable|array',
            'color_id.*' => 'exists:colors,id',
            'brand_id' => 'nullable|array',
            'brand_id.*' => 'exists:brands,id',
        ]);
        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('photo')) {
            $imagePath = $request->file('photo')->store('raw-material-photos', 'public');
        }
        // Create the raw material
        $rawMaterial = RawMaterial::create([
            'name' => $request->name,
            'raw_material_category_id' => $request->raw_material_category_id,
            'sku' => $request->sku,
            'image' => $imagePath ? 'uploads/' . $imagePath : null,
            'details' => $request->details,
            'width' => $request->width,
            'length' => $request->length,
            'density' => $request->density,
            'unit_id' => $request->unit_id,
        ]);
        // Attach sizes to the raw material
        if ($request->filled('size_id')) {
            $rawMaterial->sizes()->sync($request->size_id);
        }
        // Attach colors to the raw material
        if ($request->filled('color_id')) {
            $rawMaterial->colors()->sync($request->color_id);
        }
        // Attach brands to the raw material
        if ($request->filled('brand_id')) {
            $rawMaterial->brands()->sync($request->brand_id);
        }
        return redirect()->route('admin.materials.index')->with('success', 'Raw Material Created Successfully');
    }

    public function edit($id): View|Factory|Application
    {
        $material = RawMaterial::findOrFail($id);
        $categories = RawMaterialCategory::orderBy('id', 'DESC')->get();
        $units = Unit::orderby('id', 'DESC')->get();
        $brands = Brand::orderby('id', 'DESC')->get();
        $sizes = Size::orderby('id', 'DESC')->get();
        $colors = Color::orderby('id', 'DESC')->get();
        $brand_Id = DB::table('brand_raw_material')->where('raw_material_id', $id)->pluck('brand_id'); // Retrieves only brand_id column as a collection
        $size_Id = DB::table('size_raw_material')->where('raw_material_id', $id)->pluck('size_id');
        $color_Id = DB::table('color_raw_material')->where('raw_material_id', $id)->pluck('color_id');
        return view('admin.pages.raw-material.edit', compact('material', 'categories', 'units',
            'brands', 'sizes', 'colors', 'brand_Id', 'size_Id', 'color_Id'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'raw_material_category_id' => 'required',
            'sku' => 'required|unique:raw_materials,sku,' . $id,
            'photo' => 'nullable|image',
            'size_id' => 'nullable|array',
            'size_id.*' => 'exists:sizes,id',
            'color_id' => 'nullable|array',
            'color_id.*' => 'exists:colors,id',
            'brand_id' => 'nullable|array',
            'brand_id.*' => 'exists:brands,id',
        ]);
        $material = RawMaterial::findOrFail($id);
        // Handle image upload
        $image = $material->image ?? null;
        if ($request->hasFile('photo')) {
            // Delete previous image
            if($material->image) {
                $prev_image = $material->image;
                if (file_exists($prev_image)) {
                    unlink($prev_image);
                }
            }
            $image = 'uploads/' . $request->file('photo')->store('raw-material-photos');
        }
        // Update the raw material
        $material->update([
            'name' => $request->name,
            'raw_material_category_id' => $request->raw_material_category_id,
            'sku' => $request->sku,
            'details' => $request->details,
            'width' => $request->width,
            'length' => $request->length,
            'density' => $request->density,
            'unit_id' => $request->unit_id,
            'image' => $image,
        ]);
        // Sync sizes to the raw material
        if ($request->filled('size_id')) {
            $material->sizes()->sync($request->size_id);
        } else {
            $material->sizes()->detach(); // Remove all sizes if none are provided
        }
        // Sync colors to the raw material
        if ($request->filled('color_id')) {
            $material->colors()->sync($request->color_id);
        } else {
            $material->colors()->detach(); // Remove all colors if none are provided
        }
        // Sync brands to the raw material
        if ($request->filled('brand_id')) {
            $material->brands()->sync($request->brand_id);
        } else {
            $material->brands()->detach(); // Remove all brands if none are provided
        }
        return redirect()->route('admin.materials.index')->with('success', 'Raw Material Updated Successfully');
    }


    public function show($id): View|Factory|Application
    {
        $material = RawMaterial::findOrFail($id);
        $admins = User::all();
        $activities = AdminActivity::getActivities(RawMaterial::class, $id)->orderBy('created_at', 'desc') ->take(10) ->get();
        return view('admin.pages.raw-material.show', compact('material', 'admins', 'activities'));
    }

    public function destroy($id): RedirectResponse
    {
        $material = RawMaterial::find($id);
        $material->delete();
        return redirect()->route('admin.materials.index')->with('success', 'Raw Material Deleted Successfully');
    }

    public function getAllMaterials(): JsonResponse
    {
        $materials = RawMaterial::with(['brands','colors','sizes', 'unit'])->orderBy('id', 'DESC')->get();
        return response()->json($materials);
    }
}
