<?php

namespace App\Constants;

class ErrorCodes
{
    public const SUCCESS = 2000;

    // Authentication-related error codes

    public const REQUEST_DENIED = 4000;
    public const INVALID_CREDENTIALS = 4001;
    public const PROFILE_ALREADY_EXISTS = 4002;
    public const ACCOUNT_DISABLED = 4003;
    public const INCORRECT_PASSWORD = 4004;
    public const ACCOUNT_LOCKED = 4014;
    public const INVALID_SESSION = 4005;
    public const SESSION_EXPIRED = 4006;
    public const INVALID_USER = 4007;
    public const MULTI_FACTOR_REQUIRED = 4008;
    public const INVALID_MFA_CODE = 4009;

    // Request-related error codes
    public const INVALID_REQUEST = 4010;
    public const MISSING_REQUIRED_FIELDS = 4011;
    public const RATE_LIMIT_EXCEEDED = 4012;

    // Server-related error codes
    public const INTERNAL_SERVER_ERROR = 5000;
    public const SERVICE_UNAVAILABLE = 5001;
    public const DATABASE_ERROR = 5002;

    public const TRY_AGAIN = 5008;

}