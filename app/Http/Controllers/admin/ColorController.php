<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class ColorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('color-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $color = Color::all();
        return view('admin.pages.color.index', compact('color'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
            ]);
            $color = new Color();
            $color->color_code = $request->color_code;
            $color->name = $request->name;
            $color->save();
            Toastr::success('Color Added Successfully', 'Success');
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
            ]);
            $color = Color::find($id);
            $color->color_code = $request->color_code;
            $color->name = $request->name;
            $color->status = $request->status;
            $color->save();
            Toastr::success('Color Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $color = Color::find($id);
            $color->delete();
            Toastr::success('Color Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
