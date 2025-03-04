<?php

namespace App\Constants;

class SuccessMessages
{
    // General success messages
    public const OPERATION_SUCCESSFUL = 'Operation completed successfully';
    public const RESOURCE_CREATED = 'Resource created successfully';
    public const RESOURCE_UPDATED = 'Resource updated successfully';
    public const RESOURCE_DELETED = 'Resource deleted successfully';

    // Authentication-related success messages
    public const LOGIN_SUCCESSFUL = 'Login successful';
    public const LOGOUT_SUCCESSFUL = 'Logout successful';
    public const PASSWORD_CHANGED = 'Your password has been changed successfully';
    public const PASSWORD_RESET = 'Your password has been reset successfully';

    public const PASSWORD_RESET_TOKEN_SENT = 'Password reset token has been sent successfully to your email';
    public const ACCOUNT_CREATED = 'Account created successfully';
    public const PROFILE_CREATED = 'User profile created successfully';
    public const ACCOUNT_ACTIVATED = 'Account activated successfully';
    public const EMAIL_VERIFIED = 'Email address verified successfully';

    // Token-related success messages
    public const TOKEN_GENERATED = 'New token generated successfully';
    public const TOKEN_REFRESHED = 'Token refreshed successfully';
    public const SUCCESSFUL = 'Successful';

    // Profile-related success messages
    public const PROFILE_UPDATED = 'Profile updated successfully';
    public const SETTINGS_UPDATED = 'Settings updated successfully';

    // Multi-factor authentication success messages
    public const MFA_ENABLED = 'Multi-factor authentication enabled successfully';
    public const MFA_DISABLED = 'Multi-factor authentication disabled successfully';
    public const MFA_VERIFIED = 'Multi-factor authentication verified successfully';

    // Permission-related success messages
    public const PERMISSION_GRANTED = 'Permission granted successfully';
    public const ROLE_ASSIGNED = 'Role assigned successfully';
}