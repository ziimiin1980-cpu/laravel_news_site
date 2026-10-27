@extends('components.layout')

@section('content')

    <h2>Категории новостей</h2>
    @forelse ($categories as $category)
        <div class="news">
            <a href="{{ route('categories.show', $category) }}">{{ $category->name }}</a>
        </div>
    @empty
        <p>Категории пока не добавлены.</p>
    @endforelse

@endsection
