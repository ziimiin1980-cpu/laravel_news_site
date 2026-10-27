<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Новости</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f2f4f8;
            color: #222;
        }

        header {
            background: #202b3c;
            color: white;
            padding: 20px;
            text-align: center;
        }

        nav {
            margin-top: 15px;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 12px;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        .message {
            padding: 10px;
            margin-bottom: 15px;
            background: #e8f4e8;
            border-radius: 5px;
        }

        main {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
        }

        .news {
            padding: 15px;
            margin-bottom: 15px;
            background: #f2f4f8;
            border-radius: 8px;
        }

        input, textarea, select {
            display: block;
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            margin: 10px 0;
        }

        button {
            padding: 10px 15px;
            background: #2864dc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        footer {
            margin-top: 30px;
            padding: 20px;
            background: #202b3c;
            color: white;
            text-align: center;
        }
    </style>
</head>
<body>

    <header>
        <h1>IT Новости</h1>

        <nav>
            <a href="{{ route('home') }}">Главная</a>
            <a href="{{ route('categories.index') }}">Категории</a>
            @auth
                @if (in_array(auth()->user()->role?->name, ['journalist', 'admin'], true))
                    <a href="{{ route('journalist') }}">Журналист</a>
                @endif
                @if (auth()->user()->role?->name === 'admin')
                    <a href="{{ route('admin') }}">Админ</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit">Выйти</button>
                </form>
            @else
                <a href="{{ route('login') }}">Войти</a>
                <a href="{{ route('register') }}">Регистрация</a>
            @endauth
        </nav>
    </header>

    <main>
        @if (session('status'))
            <p class="message" role="status">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <div class="message" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        @yield('content')
    </main>

    <footer>
        <p>© 2026 IT Новости</p>
    </footer>

</body>
</html>