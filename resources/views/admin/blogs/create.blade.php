@extends('admin.layouts.app')

@section('title', 'New blog')

@section('content')
    <form method="POST" action="{{ route('admin.blogs.store') }}" enctype="multipart/form-data" id="blog-form">
        @csrf
        @include('admin.blogs._form')
    </form>
@endsection
