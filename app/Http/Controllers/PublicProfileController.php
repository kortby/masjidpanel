<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicProfileController extends Controller
{
    public function show(User $user)
    {
        $user->load(['posts' => function ($query) {
            $query->where('expires_at', '>', now())->latest();
        }, 'posts.category', 'posts.media']);

        $publicData = [
            'id' => $user->id,
            'name' => $user->name,
            'is_verified' => $user->is_verified,
            'joined_at' => $user->created_at,
            'age' => $user->show_age ? $user->age : null,
            'city' => $user->show_location ? $user->city : null,
            'zip_code' => $user->show_location ? $user->zip_code : null,
            'address' => $user->show_address ? $user->address : null,
            'phone_number' => $user->show_phone ? $user->phone_number : null,
            'email' => $user->show_email ? $user->email : null,
        ];

        return Inertia::render('Profile/Show', [
            'profile' => $publicData,
            'posts' => $user->posts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'category' => $post->category,
                    'city' => $post->city,
                    'created_at' => $post->created_at,
                    'thumb' => $post->getFirstMediaUrl('images', 'thumb') ?: null,
                ];
            }),
        ]);
    }
}
