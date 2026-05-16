<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory(5)->create([
            'is_verified' => true,
        ]);

        $locations = [
            ['city' => 'San Diego', 'zip_code' => '92101'],
            ['city' => 'Seattle', 'zip_code' => '98101'],
            ['city' => 'Portland', 'zip_code' => '97201'],
        ];

        $categories = Category::all()->keyBy('name');

        $posts = [
            // San Diego
            [
                'location' => $locations[0],
                'category' => $categories['Jobs & Hiring']->id,
                'title' => 'Experienced Halal Butcher Needed',
                'description' => 'We are looking for an experienced butcher for our local Halal market. Full-time position with benefits. Must have at least 2 years of experience handling fresh meat and maintaining sanitary standards.',
                'meta' => ['job_type' => 'Full-Time'],
                'tags' => ['Urgent', 'Halal'],
            ],
            [
                'location' => $locations[0],
                'category' => $categories['Housing & Roommates']->id,
                'title' => 'Room Available in 3BR Apartment (Brothers)',
                'description' => 'Looking for a clean and respectful brother to take the master bedroom in a 3BR/2BA apartment. Rent is $900/month plus split utilities. Very close to the local Islamic Center.',
                'meta' => ['price' => 900],
                'tags' => ['Urgent'],
            ],
            [
                'location' => $locations[0],
                'category' => $categories['Buy, Sell & Give Away']->id,
                'title' => 'Selling Like-New Toyota Camry 2020',
                'description' => 'Alhamdulillah, I am selling my 2020 Toyota Camry LE. Clean title, regular maintenance at the dealership, 45,000 miles. Great reliable car.',
                'meta' => ['price' => 18500],
                'tags' => ['Used'],
            ],
            [
                'location' => $locations[0],
                'category' => $categories['Local Services']->id,
                'title' => 'Professional Math & Science Tutoring',
                'description' => 'I am a PhD student offering tutoring in High School and College level Mathematics (Calculus, Algebra) and Physics. Flexible rates for community members.',
                'meta' => [],
            ],

            // Seattle
            [
                'location' => $locations[1],
                'category' => $categories['Jobs & Hiring']->id,
                'title' => 'Software Engineer for Islamic Tech Startup',
                'description' => 'We are building an innovative app for the Muslim community and need a Senior Backend Engineer (Laravel/Vue). Remote options available but prefer local candidates in the Seattle area.',
                'meta' => ['job_type' => 'Contract'],
                'tags' => ['Remote'],
            ],
            [
                'location' => $locations[1],
                'category' => $categories['Housing & Roommates']->id,
                'title' => 'Family House for Rent - 4 Bedrooms',
                'description' => 'Spacious 4 bedroom, 2.5 bath house available for rent starting next month. Large backyard, quiet neighborhood, great school district. Asking $3,200/month.',
                'meta' => ['price' => 3200],
            ],
            [
                'location' => $locations[1],
                'category' => $categories['Community Events']->id,
                'title' => 'Weekly Quran Tafseer Circle',
                'description' => 'Join us every Friday after Maghrib for a comprehensive study of Surah Al-Kahf. Coffee and snacks provided. All are welcome!',
                'meta' => [],
            ],
            [
                'location' => $locations[1],
                'category' => $categories['Buy, Sell & Give Away']->id,
                'title' => 'Giving away living room furniture set',
                'description' => 'We are moving out of state and giving away our sofa, coffee table, and TV stand. Free to whoever can come pick it up first. Must have your own truck.',
                'meta' => ['price' => 0],
                'tags' => ['Free', 'Used', 'Urgent'],
            ],

            // Portland
            [
                'location' => $locations[2],
                'category' => $categories['Local Services']->id,
                'title' => 'Licensed Electrician - Fair Rates',
                'description' => 'Assalamu alaikum, I am a licensed and bonded electrician with 15 years of experience. Available for residential and commercial jobs. Honest pricing and quality work.',
                'meta' => [],
            ],
            [
                'location' => $locations[2],
                'category' => $categories['Buy, Sell & Give Away']->id,
                'title' => 'Brand New Islamic Calligraphy Canvas',
                'description' => "Bought this beautiful Ayatul Kursi canvas but it doesn't fit my wall space. Original packaging still on. Dimensions are 24x36 inches.",
                'meta' => ['price' => 120],
                'tags' => ['New'],
            ],
            [
                'location' => $locations[2],
                'category' => $categories['Sports & Activities']->id,
                'title' => 'Sunday Morning Soccer Pickup Game',
                'description' => 'We play every Sunday morning at 8:00 AM at the local high school turf field. Need a few more reliable players to make it 11v11. Skill level is intermediate.',
                'meta' => [],
            ],
            [
                'location' => $locations[2],
                'category' => $categories['Marriage & Matrimony']->id,
                'title' => 'Looking for a compatible spouse',
                'description' => 'Posting on behalf of my daughter. She is 26, highly educated (Masters in Public Health), family-oriented, and practices her deen. Looking for someone with similar values.',
                'meta' => [],
            ],
        ];

        foreach ($posts as $postData) {
            $post = Post::create([
                'user_id' => $users->random()->id,
                'category_id' => $postData['category'],
                'title' => $postData['title'],
                'description' => $postData['description'],
                'city' => $postData['location']['city'],
                'zip_code' => $postData['location']['zip_code'],
                'meta' => $postData['meta'],
                'expires_at' => Carbon::now()->addDays(rand(10, 30)),
            ]);

            if (isset($postData['tags'])) {
                $tagIds = collect($postData['tags'])->map(function ($tagName) {
                    return \App\Models\Tag::firstOrCreate([
                        'slug' => \Illuminate\Support\Str::slug($tagName)
                    ], [
                        'name' => $tagName
                    ])->id;
                });
                $post->tags()->sync($tagIds);
            }
        }
    }
}
