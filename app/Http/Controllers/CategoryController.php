<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $city = $request->session()->get('location');

        $categories = Category::orderedByLocationDemand($city)->get();

        return Inertia::render('Board/Index', [
            'categories' => $categories,
            'filters' => ['location' => $city],
        ]);
    }
}
