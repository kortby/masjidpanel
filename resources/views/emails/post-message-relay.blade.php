<x-mail::message>
# New Message: {{ $post->title }}

Hi {{ $post->user->name }},

You have received a new message regarding your post **"{{ $post->title }}"** on {{ config('app.name') }}.

<x-mail::panel>
**From:** {{ $sender->name }} ({{ $sender->email }})

{{ $messageContent }}
</x-mail::panel>

<x-mail::button :url="config('app.url') . '/posts/' . $post->id">
View Post
</x-mail::button>

*To reply to this message, simply reply directly to this email.*

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
