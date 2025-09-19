<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AdminActivity;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class AssetController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkModelStatus:App\Models\Asset,asset')
            ->only(['edit', 'update', 'updateStatus', 'destroy']);
    }

    public function index(): View|Factory|Application
    {
        $asset = Asset::orderBy('id', 'DESC')->get();
        $categories = AssetCategory::orderBy('id', 'DESC')->get();
        $accounts = Account::all();
        return view('admin.pages.asset.index', compact('asset','categories', 'accounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'amount' => 'required',
            'account_id' => 'required',
            'details' => 'nullable',
            'images' => 'nullable',
        ]);
        $image = '';
        if ($request->hasFile('photo')) {
            $image = $request->file('photo')->store('asset-photo');
        }
        Asset::create([
            'name' => $request->name,
            'asset_category_id' => $request->category_id,
            'amount' => $request->amount,
            'account_id' => $request->account_id,
            'details' => $request->details,
            'images' => $image ? 'uploads/' . $image : null
        ]);
        return redirect()->route('asset.section')->with('success', 'Asset created successfully.');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $asset = Asset::find($id);
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'account_id' => 'required',
            'details' => 'nullable',
            'status' => 'required',
            'images' => 'nullable',
        ]);
        $image = $asset->images ?? null;
        if ($request->hasFile('photo')) {
            // Delete previous image
            if($asset->images) {
                $prev_image = $asset->images;
                if (file_exists($prev_image)) {
                    unlink($prev_image);
                }
            }
            $image = 'uploads/' . $request->file('photo')->store('asset-photo');
        }

        $accountId = $request->account_id ?? $asset->account_id;

        $asset->update([
            'name' => $request->name,
            'asset_category_id' => $request->category_id,
            'account_id' => $accountId,
            'amount' => $request->amount,
            'status' => $request->status,
            'details' => $request->details,
            'images' =>  $image,
        ]);

        return redirect()->route('asset.section')->with('success', 'Asset updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $asset = Asset::find($id);
        if($asset->status == 'approved') {
            return redirect()->back()->with('error', "Approved asset can't be deleted.");
        }
        if ($asset->images) {
            $previousImages = json_decode($asset->images, true);
            if ($previousImages) {
                foreach ($previousImages as $previousImage) {
                    $imagePath = public_path('uploads/' . $previousImage);
                    if (file_exists($imagePath)) {
                        unlink($imagePath); // Delete the image file
                    }
                }
            }
        }
        $asset->delete();
        return redirect()->route('asset.section')->with('success', 'Asset deleted successfully.');
    }

    public function updateStatus($id, $status): RedirectResponse
    {
        // Validate the status
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('asset.section')->with('error', 'Invalid status.');
        }
        // Find the asset
        $asset = Asset::find($id);
        if (!$asset) {
            return redirect()->back()->with('error', 'Asset not found.');
        }
        // Update the asset status
        $asset->status = $status;
        $asset->update();
        return redirect()->back()->with('success', 'Asset status updated successfully.');
    }
}
