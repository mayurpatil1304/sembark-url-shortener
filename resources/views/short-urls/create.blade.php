
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Short URL | Sembark</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 720px;
            margin: 50px auto;
            padding: 0 20px;
            color: #1f2937;
        }

        input {
            box-sizing: border-box;
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #d1d5db;
            border-radius: 6px;
        }

        button {
            padding: 12px 18px;
            border: 0;
            border-radius: 6px;
            background: #2563eb;
            color: white;
            cursor: pointer;
        }

        .message {
            margin: 16px 0;
            padding: 12px;
            background: #dcfce7;
            border-radius: 6px;
        }

        .error {
            color: #b91c1c;
        }
    </style>
</head>
<body>
    <h1>Create a short URL</h1>

    @if (session('success'))
        <div class="message">{{ session('success') }}</div>
    @endif

    @if (session('short_url'))
        <p>
            Your short URL:
            <a href="{{ session('short_url') }}">
                {{ session('short_url') }}
            </a>
        </p>
    @endif

    @if ($errors->any())
        <ul class="error">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('short-urls.store') }}">
        @csrf

        <label for="original_url">Original URL</label>

        <input
            id="original_url"
            name="original_url"
            type="url"
            placeholder="https://example.com"
            value="{{ old('original_url') }}"
            maxlength="2048"
            required
        >

        <button type="submit">Shorten URL</button>
    </form>

    <p><a href="{{ route('dashboard') }}">Back to dashboard</a></p>
</body>
</html>
