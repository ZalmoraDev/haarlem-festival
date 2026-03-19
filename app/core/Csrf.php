<?php

namespace app\core;

use app\services\exceptions\UserServExc;

/** CSRF protection utility class. */
final readonly class Csrf
{
    /** Generate or retrieve existing CSRF token. */
    public static function getToken(): string
    {
        $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
        return $_SESSION['csrf'];
    }

    /** Verify provided CSRF token against session token.
     * @throws UserServExc if token is missing or does not match. */
    public static function requireVerification(?string $token): void
    {
        if (!$token || !hash_equals($_SESSION['csrf'], $token))
            throw new UserServExc(UserServExc::CSRF_TOKEN_MISMATCH);
    }
}
