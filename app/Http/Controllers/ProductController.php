<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        return view('products.index', [
            'filters' => $request->only(['category', 'brand', 'gender', 'search', 'sort']),
        ]);
    }

    public function show(string $slug): View
    {
        return view('products.show', [
            'slug' => $slug,
        ]);
    }
}
