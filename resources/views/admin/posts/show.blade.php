@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $post->title }}</h1>

        <div class="mt-2 mb-2">Slug: {{ $post->slug }}</div>

        <p>{{ $post->content }}</p>
    </div>
@endsection