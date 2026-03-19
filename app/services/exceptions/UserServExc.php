<?php

namespace app\services\exceptions;

/** Exceptions for authentication or authorization errors. */
final class UserServExc extends BaseServExc
{
    // Not logged in
    public const string INVALID_CREDENTIALS = 'Invalid username or password,<br>please try again.';

    // Login not known
    public const string CSRF_TOKEN_MISMATCH = 'Session expired,<br>please try again.';
    public const string INSUFFICIENT_PERMISSIONS = 'You do not have sufficient permissions for this action.';

    // logged in
    public const string ALREADY_LOGGED_IN = 'You are already logged in.';

    // Password reset
    public const string PASSWORD_RESET_LINK_INVALID = 'This password reset link is invalid or expired,<br>please request a new one.';

    // User account actions
    public const string DELETION_REQUIRES_CONFIRMATION = 'You must confirm your username to delete your account.';
    public const string DELETION_NAME_MISMATCH = 'The provided username does not match your username.';
    public const string DELETION_FAILED = 'Account deletion failed due to a server error,<br>please try again later.';
}
