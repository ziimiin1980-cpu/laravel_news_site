@extends('components.layout')

@section('content')

    <h2>Панель журналиста</h2>

    <h3>Создать статью</h3>
    <form method="POST" action="{{ route('journalist.texts.store') }}">
        @csrf
        <label for="heading">Заголовок статьи</label>
        <input id="heading" name="heading" type="text" value="{{ old('heading') }}" required>

        <label for="category_id">Категория</label>
        <select id="category_id" name="category_id" required>
            <option value="">Выберите категорию</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <label for="body">Текст статьи</label>
        <textarea id="body" name="body" rows="10" required>{{ old('body') }}</textarea>
        <button type="submit">Опубликовать статью</button>
    </form>

    <h3>Мои статьи</h3>
    @forelse ($texts as $text)
        <article class="news">
            <h4>{{ $text->heading }}</h4>
            <p>{{ $text->category?->name ?? 'Без категории' }}</p>
            <a href="{{ route('journalist.texts.edit', $text) }}">Редактировать</a>
        </article>
    @empty
        <p>Вы еще не создали статьи.</p>
    @endforelse

@endsection