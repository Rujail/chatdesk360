@component('mail::message')
# Hello {{ $leadData['first_name'] }},

Thank you for reaching out to us! We have successfully received your message. Our team will review your request and get back to you as soon as possible.

@component('mail::panel')
**Your Message:**
{{ $leadData['message'] }}
@endcomponent

We will contact you at **{{ $leadData['email'] }}** or call you at **{{ $leadData['phone'] }}**.

@component('mail::button', ['url' => config('app.url'), 'color' => 'primary'])
Visit Our Website
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent