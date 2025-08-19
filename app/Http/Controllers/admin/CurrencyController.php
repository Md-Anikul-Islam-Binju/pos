<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CurrencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('currency-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }
    public function index()
    {
        $currency = Currency::all();
        return view('admin.pages.currency.index', compact('currency'));
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required',
                'name' => 'required',
                'rate' => 'required',
                'suffix' => 'required',
                'prefix' => 'required',
            ]);
            $currency = new Currency();
            $currency->code = $request->code;
            $currency->name = $request->name;
            $currency->rate = $request->rate;
            $currency->suffix = $request->suffix;
            $currency->prefix = $request->prefix;
            $currency->save();
            Toastr::success('Currency Added Successfully', 'Success');
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
                'code' => 'required',
                'name' => 'required',
                'rate' => 'required',
                'suffix' => 'required',
                'prefix' => 'required',
            ]);
            $currency = Currency::find($id);
            $currency->code = $request->code;
            $currency->name = $request->name;
            $currency->rate = $request->rate;
            $currency->suffix = $request->suffix;
            $currency->prefix = $request->prefix;
            $currency->status = $request->status;
            $currency->save();
            Toastr::success('Currency Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $currency = Currency::find($id);
            $currency->delete();
            Toastr::success('Currency Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
