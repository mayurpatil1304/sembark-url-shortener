
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accept Invitation | Sembark</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 40px 16px;
        }

        .card {
            max-width: 440px;
            margin: auto;
            padding: 28px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,.06);
        }

        label {
            display: block;
            margin-top: 16px;
            font-weight: 600;
        }

        input {
            width: 100%;
            box-sizing: border-box;
            padding: 11px;
            margin-top: 7px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
        }

        button {
            margin-top: 22px;
            padding: 12px;
            width: 100%;
            border: 0;
            border-radius: 6px;
            background: #2563eb;
            color: white;
            cursor: pointer;
        }

        .error {
            color: #b91c1c;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <main class="card">
        <h1>Accept your invitation</h1>

        <p>
            Join <strong>{{ $invitation->company->name }}</strong>
            as {{ $invitation->role }}.
        </p>

        <p>Invited email: {{ $invitation->email }}</p>

        @if ($errors->any())
            <div class="error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ route('invitations.accept', ['token' => $token]) }}">
            @csrf

            <label for="name">Your name</label>
            <input id="name" name="name" type="text"
                   value="{{ old('name') }}" required maxlength="255">

            <label for="password">Password</label>
            <input id="password" name="password" type="password"
                   required minlength="8" autocomplete="new-password">

            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation"
                   name="password_confirmation" type="password"
                   required minlength="8" autocomplete="new-password">

            <button type="submit">Create account and join</button>
        </form>
    </main>
</body>
</html>
