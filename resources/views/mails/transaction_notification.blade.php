@extends('mails.layouts.default')
@section('title', $subject)

@section('content')
    <p class="mt-2 leading-loose text-gray-600 dark:text-gray-300">
        {!! $body !!}
    </p>
@endsection
