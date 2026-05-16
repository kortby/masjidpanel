<x-mail::message>
# Your Post is Live!

Hi {{ $post->user->name }},

Great news! Your post **"{{ $post->title }}"** has been successfully published on {{ config('app.name') }}.

<x-mail::panel>
**Category:** {{ $post->category?->name }}<br>
**Location:** {{ $post->city }}<br>
**Status:** Active
</x-mail::panel>

You can view your post, share it with others, or manage it by clicking the button below. 
Any messages sent by interested users will be delivered straight to your email inbox and will also be visible at the bottom of your post page when you are logged in.

<x-mail::button :url="route('posts.show', $post)">
View Your Post
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
