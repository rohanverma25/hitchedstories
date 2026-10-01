@extends('admin.layouts.app')

@section('title', 'Edit blog')

@section('content')
    <form method="POST" action="{{ route('admin.blogs.update', $blog) }}" enctype="multipart/form-data" id="blog-form">
        @csrf
        @method('PUT')
        @include('admin.blogs._form')
    </form>
@endsection
