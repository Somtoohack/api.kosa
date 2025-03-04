@extends('mails.layouts.default')
@section('title', 'Welcome to Vobs - Your Event Booking Companion!')

@section('content')
    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 p-8 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-6">Welcome to Vobs,
            {{ $user->name ?? $user->email }}!</h1>

        <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">
            We're excited to have you join Vobs, your new platform for discovering and attending amazing events, shows, and
            conferences. Get ready to explore and book your favorite experiences!
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Here's what you can do with Vobs:</h2>

        <ul class="list-none text-gray-700 dark:text-gray-300 mb-6 space-y-4">
            <li class="flex items-start">
                <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span><strong>Discover Events:</strong> Browse a wide range of events, shows, and conferences tailored just
                    for you.</span>
            </li>
            <li class="flex items-start">
                <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span><strong>Easy Ticket Purchases:</strong> Purchase tickets quickly and securely for any event you
                    choose.</span>
            </li>
            <li class="flex items-start">
                <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span><strong>QR Code Ticketing:</strong> Receive a QR code with your ticket for hassle-free entry at the
                    event.</span>
            </li>
            <li class="flex items-start">
                <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span><strong>Manage Your Bookings:</strong> Keep track of all your upcoming events and past tickets in one
                    place.</span>
            </li>
        </ul>

        <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Get started in 4 easy steps:</h2>

        <ol class="list-decimal list-inside text-gray-700 dark:text-gray-300 mb-6 space-y-2">
            <li>Complete your profile and start browsing events.</li>
            <li>Select your favorite event and purchase tickets securely.</li>
            <li>Receive a QR code for easy access on the event day.</li>
            <li>Download our mobile app for quick access to your tickets.</li>
        </ol>

        <div class="bg-blue-100 dark:bg-blue-900 p-4 rounded-lg mb-6">
            <p class="text-blue-800 dark:text-blue-200 font-semibold">
                🎉 Special Offer: Enjoy a 10% discount on your first ticket purchase! Use code <strong>WELCOME10</strong> at
                checkout.
            </p>
        </div>

        <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">
            Our support team is here to help you have a great experience. Questions? Reach out anytime at <a
                href="mailto:support@vobs.com"
                class="text-blue-600 dark:text-blue-400 hover:underline">support@vobs.com</a>.
        </p>

        <div class="border-t border-gray-300 dark:border-gray-600 pt-6 mt-6">
            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                Enjoy the best events with ease,
            </p>
            <p class="text-lg font-bold text-blue-600 dark:text-blue-400">
                The Vobs Team
            </p>
        </div>
    </div>
@endsection
