@extends('components.layout')

@section('content')

    <h2>Последние новости</h2>

    @forelse ($news as $item)
        <article class="news">
            <h3>{{ $item->heading }}</h3>
            <p>{{ $item->body }}</p>
            <small>
                Категория: {{ $item->category?->name ?? 'Без категории' }}
                @if ($item->user)
                    | Автор: {{ $item->user->name }}
                @endif
            </small>
        </article>
    @empty
        <p>Пока нет опубликованных новостей.</p>
    @endforelse

@endsection