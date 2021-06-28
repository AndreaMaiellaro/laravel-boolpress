<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index() {
        $current_user = Auth::user();

        $data = [
            'current_user' => $current_user,
            'current_user_info' => $current_user->userInfo
        ];

        return view('admin.home', $data);
    }
}
