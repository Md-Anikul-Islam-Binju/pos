<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminActivity;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Size;
use App\Models\Unit;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class ProductController extends Controller
{
    public function index(): View|Factory|Application
    {
        $products = Product::orderBy('id', 'DESC')->get();
        return view('admin.pages.product.index', compact('products'));
    }

    public function create(): View|Factory|Application
    {
        $categories = ProductCategory::all();
        $units = Unit::all();
        return view('admin.pages.product.create', compact('categories', 'units'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required',
            'details' => 'nullable|string',
            'short_description' => 'nullable|string',
            'sku' => 'nullable|string',
            'unit_id' => 'nullable|integer',
            'width' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'density' => 'nullable|numeric',
            'thumbnail' => 'required|image',
            'product_images.*' => 'nullable|image',
        ]);
        $slug = Str::slug($request->input('name'));

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('products/thumbnails', 'public');
        }

        $imagePaths = [];
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $image) {
                $imagePaths[] = $image->store('products/images', 'public');
            }
        }

        $product = Product::create([
            'name' => $request->input('name'),
            'slug' => $slug,
            'category_id' => $request->input('category_id'),
            'details' => $request->input('details'),
            'short_details' => $request->input('short_description'),
            'sku' => $request->input('sku'),
            'unit_id' => $request->input('unit_id'),
            'width' => $request->input('width'),
            'length' => $request->input('length'),
            'density' => $request->input('density'),
            'thumbnail' => $thumbnailPath ?? null,
            'images' => json_encode($imagePaths),
        ]);

        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }

    public function edit($id): View|Factory|Application
    {
        $product = Product::find($id);
        $categories = ProductCategory::all();
        $units = Unit::all();
        return view('admin.pages.product.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required',
            'details' => 'nullable|string',
            'short_description' => 'nullable|string',
            'sku' => 'nullable|string',
            'unit_id' => 'nullable|integer',
            'width' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'density' => 'nullable|numeric',
            'thumbnail' => 'nullable|image',
            'product_images.*' => 'nullable|image',
        ]);

        // Generate a new slug only if the name has changed
        $slug = $product->name === $request->input('name') ? $product->slug : Str::slug($request->input('name'));

        // Handle thumbnail update
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if it exists
            if ($product->thumbnail) {
                unlink(public_path('uploads/' . $product->thumbnail));  // Directly delete the file from the public directory
            }
            $thumbnailPath = $request->file('thumbnail')->store('products/thumbnails', 'public');
        } else {
            $thumbnailPath = $product->thumbnail;  // Keep the old thumbnail if not updated
        }

        // Handle product images update
        $imagePaths = json_decode($product->images) ?: []; // Existing image paths

        if ($request->hasFile('product_images')) {
            // Store new images
            foreach ($request->file('product_images') as $image) {
                $newImagePath = $image->store('products/images', 'public');
                $imagePaths[] = $newImagePath;
            }
        }

        // Update product
        $product->update([
            'name' => $request->input('name'),
            'slug' => $slug,  // Use the original or new slug
            'category_id' => $request->input('category_id'),
            'details' => $request->input('details'),
            'short_details' => $request->input('short_description'),
            'sku' => $request->input('sku'),
            'unit_id' => $request->input('unit_id'),
            'width' => $request->input('width'),
            'length' => $request->input('length'),
            'density' => $request->input('density'),
            'thumbnail' => $thumbnailPath,  // Use new or existing thumbnail
            'images' => json_encode($imagePaths), // Encode the updated image paths
        ]);

        return redirect()->route('.products.index')->with('success', 'Product updated successfully');
    }


    public function destroy($id): RedirectResponse
    {
        $product = Product::find($id);
        $product->delete();
        return redirect()->route('.products.index')->with('success', 'Product Deleted Successfully');
    }

    public function show($id): View|Factory|Application
    {
        $product = Product::findOrFail($id);
        $admins = User::all();
        $activities = AdminActivity::getActivities(Product::class, $id)->orderBy('created_at', 'desc')->take(10)->get();
        return view('admin.pages.product.show', compact('product', 'admins', 'activities'));
    }

    public function deleteImage(Request $request, $productId, $key)
    {
        // Find the product using the provided ID
        $product = Product::findOrFail($productId);

        // Decode the images
        $imagePaths = json_decode($product->images, true);

        // Check if the image at the specified key exists
        if (isset($imagePaths[$key])) {
            // Get the full path of the image
            $imagePath = public_path('uploads/' . $imagePaths[$key]);

            // Delete the image file from the server if it exists
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Remove the image path from the array
            unset($imagePaths[$key]);

            // Update the product's images, re-index the array
            $product->images = json_encode(array_values($imagePaths));
            $product->save();

            // Return a response indicating success
            return response()->json(['message' => 'Image deleted successfully']);
        }

        // Return a response indicating the image was not found
        return response()->json(['message' => 'Image not found'], 404);
    }

    public function deleteThumb(Request $request, $productId)
    {
        // Find the product using the provided ID
        $product = Product::findOrFail($productId);

        // Define the path to the thumbnail image
        $imagePath = public_path('uploads/' . $product->thumbnail); // Ensure you provide the full path

        // Check if the thumbnail exists and delete it if it does
        if (file_exists($imagePath)) {
            unlink($imagePath); // Delete the image file
        }

        // Clear the thumbnail field in the database
        $product->thumbnail = null; // Or set it to '' if you prefer
        $product->save(); // Save the changes

        // Return a response indicating success
        return response()->json(['message' => 'Thumbnail deleted successfully']);
    }

    public function getAllProducts(): JsonResponse
    {
        $products = Product::with(['unit'])->orderBy('id', 'DESC')->get();
        return response()->json($products);
    }
}
