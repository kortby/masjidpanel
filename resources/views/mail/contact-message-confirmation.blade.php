<x-mail::message>
# Thank you, {{ $contactMessage->name }}!

We have received your message and will get back to you within 24 hours.

**Your message:**

<x-mail::panel>
**Subject:** {{ $contactMessage->subject }}

{{ $contactMessage->message }}
</x-mail::panel>

If you need immediate assistance, feel free to call us at **+1 (619) 742-7188**.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
