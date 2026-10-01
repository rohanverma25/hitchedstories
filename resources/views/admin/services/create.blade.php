@extends('admin.layouts.app')

@section('title', 'New service')

@section('content')
    <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.services._form')
    </form>
@endsection
