<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in — Hosting Tracker</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex min-h-screen items-center justify-center bg-gray-900 px-4">
    <div class="w-full max-w-sm">
        <div class="mb-6 flex items-center justify-center gap-2">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-500 font-bold text-white">H</span>
            <span class="text-white text-xl font-semibold">Hosting Tracker</span>
        </div>

        <div class="card p-6">
            <h1 class="mb-6 text-lg font-semibold">Log in</h1>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800 ring-1 ring-inset ring-red-600/20">
                    These credentials do not match our records.
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="label">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="input mt-1">
                </div>

                <div>
                    <label for="password" class="label">Password</label>
                    <input id="password" type="password" name="password" required class="input mt-1">
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="remember" value="1" class="rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                    Remember me
                </label>

                <button type="submit" class="btn btn-primary w-full">Log in</button>
            </form>
        </div>
    </div>
</body>

</html>
