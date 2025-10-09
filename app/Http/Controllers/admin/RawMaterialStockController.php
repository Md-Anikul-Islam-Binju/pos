<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\RawMaterialStock;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class RawMaterialStockController extends Controller
{
    public function index(): View|Factory|Application
    {
        $stocks = RawMaterialStock::latest()->get();
        return view('admin.pages.raw-material-stock.index', compact('stocks'));
    }

    public function show($id): View|Factory|Application
    {
        $stock = RawMaterialStock::findOrFail($id);
        return view('admin.pages.rawMaterial-stock.show', compact('stock'));
    }
}
