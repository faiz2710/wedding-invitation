
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Wedding Invitation</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
            background: #f7f1e8;
            color: #30251f;
            font-family: Georgia, serif;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 36px;
            background: #fffdf8;
            border: 1px solid #d7c3b2;
            box-shadow: 0 15px 45px #4c263014;
        }

        .ornament {
            color: #aa8a4b;
            text-align: center;
            font-size: 32px;
        }

        h1 {
            margin: 10px 0;
            color: #6e2638;
            text-align: center;
            font-weight: normal;
        }

        p {
            color: #78665c;
            text-align: center;
            line-height: 1.6;
        }

        label {
            display: block;
            margin: 24px 0 8px;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #d7c3b2;
            background: white;
            font: inherit;
        }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 14px;
            border: 0;
            background: #6e2638;
            color: white;
            font: inherit;
            cursor: pointer;
        }

        .error {
            margin-top: 12px;
            color: #a32835;
            font-size: 13px;
        }

        .success {
            color: #35633b;
            font-size: 13px;
        }

        a {
            display: block;
            margin-top: 24px;
            color: #6e2638;
            text-align: center;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <main class="login-card">
        <div class="ornament">❦</div>
        <h1>Welcome Back</h1>
        <p>Masuk ke dashboard pengelolaan undangan.</p>

        @if (session('success'))
            <p class="success">{{ session('success') }}</p>
        @endif

        <form action="{{ route('admin.authenticate') }}" method="POST">
            @csrf

            <label for="password">Password admin</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Masukkan password admin"
                autocomplete="current-password"
                required
                autofocus
            >

            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror

            <button type="submit">Masuk Dashboard</button>
        </form>

        <a href="{{ route('invitation') }}">Kembali ke undangan</a>
    </main>
</body>
</html>
