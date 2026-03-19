<?php

namespace app\services;

use app\services\exceptions\ValidationServExc;

/** Service for validating Google ReCaptcha v2 tokens */
final readonly class ReCaptchaServ
{
    private string $secretKey;

    public function __construct()
    {
        $this->secretKey = $_ENV['RECAPTCHA_SECRET_KEY'] ?? '';
    }

    /**
     * Validates a ReCaptcha token with Google's API
     * @param string|null $token The ReCaptcha token from the form submission
     * @throws ValidationServExc if validation fails
     */
    public function validateToken(?string $token): void
    {
        // Check if token exists
        if (empty($token))
            throw new ValidationServExc(ValidationServExc::RECAPTCHA_REQUIRED);

        // Check if secret key is configured
        if (empty($this->secretKey)) {
            error_log("ReCaptcha secret key not configured in environment");
            throw new ValidationServExc(ValidationServExc::RECAPTCHA_FAILED);
        }

        // Prepare verification request
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $this->secretKey,
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ];

        // Initialize cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        // Execute request
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for cURL errors
        if ($response === false || $httpCode !== 200) {
            error_log("ReCaptcha API request failed: " . $curlError);
            throw new ValidationServExc(ValidationServExc::RECAPTCHA_FAILED);
        }

        // Parse response
        $result = json_decode($response, true);

        // Validate response
        if (!isset($result['success']) || $result['success'] !== true) {
            $errorCodes = $result['error-codes'] ?? ['unknown-error'];
            error_log("ReCaptcha validation failed: " . implode(', ', $errorCodes));
            throw new ValidationServExc(ValidationServExc::RECAPTCHA_INVALID);
        }
    }
}
