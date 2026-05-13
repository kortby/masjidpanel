<p>Hi {{ $post->user->name }},</p>

<p>You have received a new message regarding your post <strong>"{{ $post->title }}"</strong> on {{ config('app.name') }}.</p>

<hr>

<p><strong>From:</strong> {{ $sender->name }} ({{ $sender->email }})</p>

<p><strong>Message:</strong></p>
<p>{!! nl2br(e($messageContent)) !!}</p>

<hr>

<p><em>To reply to this message, simply reply directly to this email.</em></p>
