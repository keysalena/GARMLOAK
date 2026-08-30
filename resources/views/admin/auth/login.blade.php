<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login | GARML</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="login-page">
    <div class="login-shell">
        <div class="login-panel">
            <div class="login-brand">
                <div class="brand-mark">G</div>
                <div>
                    <p class="eyebrow">Admin Portal</p>
                    <h1>GARML</h1>
                </div>
            </div>

            <div class="login-copy">
                <h2>Masuk ke pusat kendali</h2>
                <p>Kelola kelas, modul, materi, challenge, dan konten AR untuk program belajar.</p>
            </div>

            @if ($errors->any())
            <div class="alert alert-error">
                {{ $errors->first() }}
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="login-form">
                @csrf

                <label class="field-group">
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@garml.id" required />
                </label>

                <label class="field-group">
                    <span>Password</span>
                    <input type="password" name="password" placeholder="••••••••" required />
                </label>

                <button type="submit" class="btn btn-primary btn-block">Masuk</button>
            </form>
        </div>
    </div>
</body>

</html>