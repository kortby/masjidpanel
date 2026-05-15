<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Jobs & Hiring',
            'Housing & Roommates',
            'Marriage & Matrimony',
            'Professional Networking',
            'Buy, Sell & Give Away',
            'Education & Tutors',
            'Local Services',
            'Sports & Activities',
            'Rideshare & Carpool',
            'Community Events',
            'Websites & Apps',
            'Other (Suggest a Category)',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate([
                'name' => $category,
                'slug' => Str::slug($category),
            ]);
        }
    }
}
