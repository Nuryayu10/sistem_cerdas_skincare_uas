<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Rekomendasi Skincare KNN</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-b from-rose-50 to-white min-h-screen flex flex-col">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-sm border-b border-rose-100 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-xl font-bold text-rose-600">✨ SkincareMatch</a>
            <div class="flex items-center space-x-4 text-sm font-medium">
                <a href="{{ route('home') }}#edukasi" class="text-gray-600 hover:text-rose-500">Edukasi</a>
                @auth
                    <a href="{{ route('recommend.form') }}" class="text-rose-600 font-semibold hover:underline">Cari Rekomendasi</a>
                    <a href="{{ route('chat.index') }}" class="text-rose-600 font-semibold hover:underline">Chat AI</a>
                    <span class="text-gray-400">|</span>
                    <span class="text-gray-700 font-normal">Halo, <b>{{ Auth::user()->name }}</b></span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-gray-100 hover:bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-xs transition">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-rose-600 hover:text-rose-700">Masuk</a>
                    <a href="{{ route('register') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-4 py-2 rounded-lg transition">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 py-8 flex-grow w-full">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-rose-100 py-4 text-center text-xs text-gray-400">
        &copy; {{ date('Y') }} Sistem Rekomendasi Skincare KNN
    </footer>
</body>
</html>