@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none; gap: 8px;">
{{-- <img src="{{ asset('logo.png') }}" class="logo" alt="{{ config('app.name') }} Logo" style="height: 32px; width: 32px; margin: 0;"> --}}
<span style="font-size: 20px; font-weight: 800; color: #064e3b; margin-left: 8px;">{{ config('app.name') }}</span>
</a>
</td>
</tr>
