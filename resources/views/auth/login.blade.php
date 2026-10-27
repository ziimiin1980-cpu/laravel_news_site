@extends('components.layout')

@section('content')

    <h2>Вход</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="username">Логин</label>
        <input id="username" name="username" value="{{ old('username') }}" required>

        <label for="password">Пароль</label>
        <input id="password" name="password" type="password" required>

        <button type="submit">Войти</button>
    </form>

@endsection
