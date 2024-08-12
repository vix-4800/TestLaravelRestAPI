<x-mail::message>
    # Reset Password

    Your password has been reset.

    Email: {{ $userEmail }}

    New Password: {{ $userPassword }}

    Thanks,
    {{ config('app.name') }}
</x-mail::message>
