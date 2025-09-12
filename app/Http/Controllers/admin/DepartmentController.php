<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('department-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }
    public function index()
    {
        $department = Department::all();
        return view('admin.pages.department.index', compact('department'));
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
            ]);
            $department = new Department();
            $department->name = $request->name;
            $department->save();
            Toastr::success('Department Added Successfully', 'Success');
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
            $department = Department::find($id);
            $department->name = $request->name;
            $department->status = $request->status;
            $department->save();
            Toastr::success('Department Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $department = Department::find($id);
            $department->delete();
            Toastr::success('Department Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
