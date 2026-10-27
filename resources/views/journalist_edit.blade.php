@extends('components.layout')

@section('content')

    <h2>Редактирование статьи</h2>
    <form method="POST" action="{{ route('journalist.texts.update', $text) }}">
        @csrf
        @method('PUT')

        <label for="heading">Заголовок статьи</label>
        <input id="heading" name="heading" value="{{ old('heading', $text->heading) }}" required>

        <label for="category_id">Категория</label>
        <select id="category_id" name="category_id" required>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $text->category_id) == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <label for="body">Текст статьи</label>
        <textarea id="body" name="body" rows="10" required>{{ old('body', $text->body) }}</textarea>
        <button type="submit">Сохранить изменения</button>
    </form>

    <p><a href="{{ route('journalist') }}">Вернуться к моим статьям</a></p>

@endsection
