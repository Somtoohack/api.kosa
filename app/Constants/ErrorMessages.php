<?php

namespace App\Constants;

class ErrorMessages
{
    public const SUCCESS = 'Operation completed successfully';

    // Authentication-related error messages
    public const INVALID_CREDENTIALS = 'Invalid username or password';
    public const INCORRECT_PASSWORD = 'Incorrect password';
    public const INVALID_USER = 'User does not exist';
    public const ACCOUNT_LOCKED = 'Account has been locked due to multiple failed attempts';
    public const ACCOUNT_DISABLED = 'This account has been disabled';
    public const INVALID_SESSION = 'The provided authentication token is invalid';
    public const PROFILE_ALREADY_EXISTS = 'Profile already exist';
    public const SESSION_EXPIRED = 'Invalid session';
    public const MULTI_FACTOR_REQUIRED = 'Multi-factor authentication is required';
    public const INVALID_MFA_CODE = 'The provided multi-factor authentication code is invalid';

    // Request-related error messages
    public const INVALID_REQUEST = 'The request is invalid';
    public const INVALID_ROUTE = 'Invalid route';
    public const INVALID_REQUEST_PATTERN = 'Invalid request pattern';
    public const INVALID_SOURCE_TARGET = 'Invalid Source or Target';
    public const REQUEST_DENIED = 'Request denied';
    public const MISSING_REQUIRED_FIELDS = 'One or more required fields are missing';
    public const RATE_LIMIT_EXCEEDED = 'Rate limit has been exceeded. Please try again later';

    // Server-related error messages
    public const INTERNAL_SERVER_ERROR = 'An unexpected error occurred on the server';
    public const SERVICE_UNAVAILABLE = 'The service is temporarily unavailable';
    public const DATABASE_ERROR = 'A database error occurred';
}