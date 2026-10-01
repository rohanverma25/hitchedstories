@extends('admin.layouts.app')

@section('title', 'New review')

@section('content')
    <form method="POST" action="{{ route('admin.reviews.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.reviews._form')
    </form>
@endsection
