@extends('mails.layouts.default')
@section('title', 'Password Reset Token')

@section('content')

    <p class="mt-2 leading-loose text-gray-600 dark:text-gray-300">
        We have received your request to reset your password.
    </p>

    <p class="mt-2 leading-loose text-gray-600 dark:text-gray-300">
        Your password reset token is:
    </p>


    <div class="flex items-center mt-4 gap-x-4">
        @foreach (str_split($token) as $digit)
            <p class="flex items-center justify-center w-10 h-10 text-2xl font-medium beamer-primary border rounded-md">
                {{ $digit }}</p>
        @endforeach
    </div>

    <p class="mt-4 leading-loose text-gray-600 dark:text-gray-300">
        Use this token to reset your password, and keep it confidential.
    </p>
@endsection
