@extends('admin.layouts.app')

@section('title', 'Edit review')

@section('content')
    <form method="POST" action="{{ route('admin.reviews.update', $review) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.reviews._form')
    </form>
@endsection
