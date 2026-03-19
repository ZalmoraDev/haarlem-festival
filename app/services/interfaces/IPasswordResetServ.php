<?php

namespace app\services\interfaces;

use app\services\exceptions\UserServExc;
use app\services\exceptions\ValidationServExc;

/** Service contract for password reset workflow. */
interface IPasswordResetServ
{
    /** Starts the password reset flow by validating the email and sending a reset link.
     * @param string $email User's email address
     * @throws ValidationServExc if email format is invalid
     */
    public function request(string $email): void;

    /** Completes the password reset flow for a valid token.
     * @param string $resetToken Raw reset token from email link (hex string, 64 chars)
     * @param string $password New password
     * @param string $passwordConfirm New password confirmation
     * @throws ValidationServExc if password validation fails or fields are empty
     * @throws UserServExc if token is invalid, expired, or not found
     */
    public function resetPassword(string $resetToken, string $password, string $passwordConfirm): void;
}
