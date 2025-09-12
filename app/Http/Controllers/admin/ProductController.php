<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('product-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $product = Product::all();
        $productCategory = ProductCategory::all();
        $unit = Unit::all();
        return view('admin.pages.product.index', compact('product', 'productCategory', 'unit'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'category_id' => 'required',
                'sku' => 'required',
                'unit_id' => 'required',
                'width' => 'required',
                'length' => 'required',
                'density' => 'required',
            ]);

            $slug = Str::slug($request->name);
            if (Product::where('slug', $slug)->where('id', '!=', $id ?? null)->exists()) {
                Toastr::error('Slug already exists', 'Error');
                return redirect()->back();
            }

            $product = new Product();
            $product->name = $request->name;
            $product->slug = $slug;
            $product->category_id = $request->category_id;
            $product->sku = $request->sku;
            $product->unit_id = $request->unit_id;
            $product->width = $request->width;
            $product->length = $request->length;
            $product->density = $request->density;
            $product->save();

            Toastr::success('Product Added Successfully', 'Success');
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
                'category_id' => 'required',
                'sku' => 'required',
                'unit_id' => 'required',
                'width' => 'required',
                'length' => 'required',
                'density' => 'required',
            ]);

            $product = Product::find($id);
            $product->name = $request->name;
            if($product->name === $request->name) {
                $product->slug = $product->slug;
            } else {
                $slug = Str::slug($request->name);
                if (Product::where('slug', $slug)->where('id', '!=', $id ?? null)->exists()) {
                    Toastr::error('Slug already exists', 'Error');
                    return redirect()->back();
                }
                $product->slug = $slug;
            }
            $product->category_id = $request->category_id;
            $product->sku = $request->sku;
            $product->unit_id = $request->unit_id;
            $product->width = $request->width;
            $product->length = $request->length;
            $product->density = $request->density;
            $product->save();

            Toastr::success('Product Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            $product->delete();
            Toastr::success('Product Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function getAllProducts()
    {
        $products = Product::with(['unit'])->orderBy('id', 'DESC')->get();
        return response()->json($products);
    }
}
