<?php

namespace app\repositories\exceptions;

/** Exception for user repository operations. */
final class UserRepoExc extends BaseRepoExc
{
    // Query errors
    public const string FAILED_TO_FETCH_USER = "Failed to fetch user";
    public const string FAILED_TO_CHECK_USERNAME = "Failed to check if username exists";
    public const string FAILED_TO_CHECK_EMAIL = "Failed to check if email exists";
    public const string FAILED_TO_CREATE_USER = "Failed to create user";
    public const string FAILED_TO_STORE_PASSWORD_RESET_TOKEN = "Failed to store password reset token";
    public const string FAILED_TO_FETCH_PASSWORD_RESET_TOKEN = "Failed to fetch password reset token";
    public const string FAILED_TO_CONSUME_PASSWORD_RESET_TOKEN = "Failed to consume password reset token";


    // Modification errors
    public const string USER_NOT_FOUND = "User not found";
    public const string FAILED_TO_UPDATE_USER = "Failed to update user";
    public const string FAILED_TO_DELETE_USER = "Failed to delete user";
    public const string FAILED_TO_UPDATE_PASSWORD = "Failed to update password";
}
