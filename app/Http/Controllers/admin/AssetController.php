<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AssetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('asset-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }
    public function index()
    {
        $asset = Asset::all();
        $account = Account::all();
        return view('admin.pages.asset.index', compact('asset', 'account'));
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'category_id' => 'required',
                'amount' => 'required',
                'account_id' => 'required',
            ]);

            $asset = new Asset();
            $asset->name = $request->name;
            $asset->category_id = $request->category_id;
            $asset->amount = $request->amount;
            $asset->account_id = $request->account_id;
            $asset->save();
            Toastr::success('Asset Added Successfully', 'Success');
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
                'amount' => 'required',
                'account_id' => 'required',
            ]);
            $asset = Asset::find($id);
            $asset->name = $request->name;
            $asset->category_id = $request->category_id;
            $asset->amount = $request->amount;
            $asset->account_id = $request->account_id;
            $asset->status = $request->status;
            $asset->save();
            Toastr::success('Asset Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $asset = Asset::find($id);
            $asset->delete();
            Toastr::success('Asset Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updateStatus($id, $status): \Illuminate\Http\RedirectResponse
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
