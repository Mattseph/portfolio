<x-mail::message>
# New contact message

**From:** {{ $contact->name }} <{{ $contact->email }}>
**Subject:** {{ $contact->subject }}

{{ $contact->message }}

— Portfolio
</x-mail::message>
