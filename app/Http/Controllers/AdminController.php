<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategorySuggestion;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index()
    {
        $metrics = [
            'total_users' => User::count(),
            'verified_users' => User::where('is_verified', true)->count(),
            'active_posts' => Post::where('expires_at', '>', now())->count(),
            'pending_suggestions' => CategorySuggestion::where('status', 'pending')->count(),
        ];

        $suggestions = CategorySuggestion::with(['user', 'post'])
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->map(function ($suggestion) {
                return [
                    'id' => $suggestion->id,
                    'suggested_name' => $suggestion->suggested_name,
                    'user_name' => $suggestion->user->name,
                    'post_title' => $suggestion->post ? $suggestion->post->title : 'Deleted Post',
                    'post_id' => $suggestion->post_id,
                    'created_at' => $suggestion->created_at,
                ];
            });

        $users = User::withCount('posts')
            ->latest()
            ->paginate(50)
            ->through(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_verified' => (bool) $user->is_verified,
                'posts_count' => $user->posts_count,
                'created_at' => $user->created_at,
            ]);

        $categories = Category::withCount('posts')->orderBy('name')->get();

        $messages = ContactMessage::latest()->get();

        return Inertia::render('Admin/Dashboard', [
            'metrics' => $metrics,
            'suggestions' => $suggestions,
            'users' => $users,
            'categories' => $categories,
            'messages' => $messages,
        ]);
    }

    public function verifyUser(User $user)
    {
        $user->update(['is_verified' => true]);

        return redirect()->back()->with('success', 'User has been manually verified.');
    }

    public function destroyUser(User $user)
    {
        if ($user->hasRole('Super Admin')) {
            return redirect()->back()->with('error', 'Cannot delete Super Admin accounts.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User and all associated posts have been deleted.');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,'.$category->id],
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function destroyCategory(Category $category)
    {
        if ($category->posts()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete a category that has posts. Reassign posts first.');
        }

        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }

    public function toggleReadMessage(ContactMessage $message)
    {
        $message->update(['is_read' => ! $message->is_read]);

        return redirect()->back();
    }

    public function destroyMessage(ContactMessage $message)
    {
        $message->delete();

        return redirect()->back()->with('success', 'Message deleted.');
    }
}
