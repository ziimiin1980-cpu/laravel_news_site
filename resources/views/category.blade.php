@extends('components.layout')

@section('content')

    <h2>Категория: {{ $category->name }}</h2>

    @forelse ($news as $item)
        <article class="news">
            <h3>{{ $item->heading }}</h3>
            <p>{{ $item->body }}</p>
            @if ($item->user)
                <small>Автор: {{ $item->user->name }}</small>
            @endif
        </article>
    @empty
        <p>В этой категории пока нет опубликованных новостей.</p>
    @endforelse

@endsection