@extends('components.layout')

@section('content')

    <h2>Панель администратора</h2>

    <h3>Управление статьями</h3>
    @forelse ($texts as $text)
        <article class="news">
            <form method="POST" action="{{ route('admin.texts.update', $text) }}">
                @csrf
                @method('PUT')

                <label for="heading-{{ $text->id }}">Заголовок</label>
                <input id="heading-{{ $text->id }}" name="heading" value="{{ $text->heading }}" required>

                <label for="category-{{ $text->id }}">Категория</label>
                <select id="category-{{ $text->id }}" name="category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($text->category_id == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <label for="body-{{ $text->id }}">Текст статьи</label>
                <textarea id="body-{{ $text->id }}" name="body" rows="5" required>{{ $text->body }}</textarea>
                <p>Автор: {{ $text->user?->name ?? 'Не указан' }}</p>
                <button type="submit">Сохранить</button>
            </form>

            <form method="POST" action="{{ route('admin.texts.delete', $text) }}">
                @csrf
                @method('DELETE')
                <button type="submit">Удалить статью</button>
            </form>
        </article>
    @empty
        <p>Статей пока нет.</p>
    @endforelse

    <h3>Пользователи</h3>
    @forelse ($users as $user)
        <article class="news">
            <h4>{{ $user->name }} ({{ '@'.$user->username }})</h4>
            <p>Текущая роль: {{ $user->role?->name ?? 'не назначена' }}</p>
            <p>Статус: {{ $user->is_blocked ? 'заблокирован' : 'активен' }}</p>

            <form method="POST" action="{{ route('admin.users.role', $user) }}">
                @csrf
                @method('PATCH')
                <label for="role-{{ $user->id }}">Роль пользователя</label>
                <select id="role-{{ $user->id }}" name="role_id" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected($user->role_id == $role->id)>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit">Изменить роль</button>
            </form>

            <form method="POST" action="{{ route('admin.users.block', $user) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="is_blocked" value="{{ $user->is_blocked ? 0 : 1 }}">
                <button type="submit">
                    {{ $user->is_blocked ? 'Разблокировать' : 'Заблокировать' }}
                </button>
            </form>
        </article>
    @empty
        <p>Пользователей пока нет.</p>
    @endforelse

@endsection
