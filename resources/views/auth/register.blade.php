@extends('components.layout')

@section('content')

    <h2>Регистрация</h2>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <label for="username">Логин</label>
        <input id="username" name="username" value="{{ old('username') }}" required>

        <label for="name">Имя</label>
        <input id="name" name="name" value="{{ old('name') }}" required>

        <label for="email">Электронная почта</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required>

        <label for="password">Пароль</label>
        <input id="password" name="password" type="password" required>

        <label for="password_confirmation">Повторите пароль</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required>

        <button type="submit">Зарегистрироваться</button>
    </form>

@endsection
