<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            \App\Models\Category::firstOrCreate([
                'name' => $category,
                'slug' => \Illuminate\Support\Str::slug($category),
            ]);
        }
    }
}
