<!DOCTYPE html>
<html>
<body>
    <p>Hi {{ $user->name ?? 'there' }},</p>

    <p>Your account has been approved. You can now log in using the link below:</p>

    <p><a href="{{ route('login') }}">{{ route('login') }}</a></p>

    <p>Thank you.</p>
</body>
</html>
