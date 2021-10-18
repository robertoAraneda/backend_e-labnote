<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
    <div style="background-color: #3999BF; color: #ffffff; font-size: 30px; padding: 1em 2em">
        {{ $slot }}
    </div>
@endif
</a>
</td>
</tr>
