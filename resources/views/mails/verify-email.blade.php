<!-- resources/views/emails/verify-email.blade.php -->

@extends('mails.layouts.default')
@section('title', 'Verify your email')
@section('content')
    <h2 class="text-gray-700 dark:text-gray-200">Hi {{ $user->name ?? $user->email }},</h2>

    <p class="mt-2 leading-loose text-gray-600 dark:text-gray-300">
        This is your verification code:
    </p>

    <div class="flex items-center mt-4 gap-x-4">
        @foreach (str_split($code) as $digit)
            <p class="flex items-center justify-center w-10 h-10 text-2xl font-medium beamer-primary border rounded-md">
                {{ $digit }}</p>
        @endforeach
    </div>

    <p class="mt-4 leading-loose text-gray-600 dark:text-gray-300">
        This code will only be valid for the next 5 minutes. If the code does not work, you can request another one
    </p>
@endsection
