@extends('mails.layouts.main')
@section('title', $subject)

@section('content')
    <p style="font-size:1rem;line-height:1.5rem;margin:16px 0">
        {!! $body !!}
    </p>
@endsection
