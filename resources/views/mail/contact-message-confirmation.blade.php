<x-mail::message>
# Thank you, {{ $contactMessage->name }}!

We have received your message and will get back to you within 24 hours.

**Your message:**

---

**Subject:** {{ $contactMessage->subject }}

{{ $contactMessage->message }}

---

If you need immediate assistance, feel free to call us at **+1 (619) 742-7188**.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
