<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategorySuggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategorySuggestionController extends Controller
{
    public function approve(Request $request, CategorySuggestion $suggestion)
    {
        // 1. Create the new category
        $slug = Str::slug($suggestion->suggested_name);
        
        $category = Category::firstOrCreate(
            ['slug' => $slug],
            ['name' => $suggestion->suggested_name]
        );

        // 2. Reassign the post
        if ($suggestion->post) {
            $suggestion->post->update(['category_id' => $category->id]);
        }

        // 3. Mark as approved
        $suggestion->update(['status' => 'approved']);

        return back()->with('success', 'Category approved and post reassigned successfully!');
    }

    public function reject(Request $request, CategorySuggestion $suggestion)
    {
        // Mark as rejected
        $suggestion->update(['status' => 'rejected']);

        return back()->with('success', 'Suggestion rejected.');
    }
}
