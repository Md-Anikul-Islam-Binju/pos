<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
       $loginLog = LoginLog::orderBy('last_login','desc')->get();
       return view('admin.dashboard', compact('loginLog'));
    }

    public function unauthorized()
    {
        return view('admin.unauthorized');
    }





}
