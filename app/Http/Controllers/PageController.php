<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        // например, slug "home"
        $page = Page::where('slug', 'home')->where('is_active', true)->firstOrFail();
        // dd($page);
        return view('pages.show', compact('page'));
    }

    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view('pages.show', compact('page'));
    }
}
