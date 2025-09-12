<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductStock;
use Illuminate\Support\Facades\Gate;

class ProductStockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('product-stock-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $stock = ProductStock::all();
        return view('admin.pages.product-stock.index', compact('stock'));
    }
}
