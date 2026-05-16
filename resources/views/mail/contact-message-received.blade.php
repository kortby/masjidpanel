<x-mail::message>
# New Contact Message

You have received a new message from the MasjidPanel contact form.

**From:** {{ $contactMessage->name }} ({{ $contactMessage->email }})

**Subject:** {{ $contactMessage->subject }}

---

{{ $contactMessage->message }}

---

<x-mail::button :url="config('app.url') . '/admin/dashboard'">
View in Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
