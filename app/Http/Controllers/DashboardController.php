<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with(['user','images'])->latest()->paginate(10);
        return view('admin.dashboard', compact('posts'));
    }
}
