<?php

namespace App\Http\Controllers\admin;

use App\Models\RawMaterialStock;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class RawMaterialStockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('material-stock-list')) {
                return redirect()->route('unauthorized.action');
            }
            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $stock = RawMaterialStock::all();
        return view('admin.pages.raw-material-stock.index', compact('stock'));
    }

    public function show($id)
    {
        $stock = RawMaterialStock::findOrFail($id);
        return view('admin.pages.raw-material-stock.show', compact('stock'));
    }
}
