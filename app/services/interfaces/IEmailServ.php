<?php

namespace app\services\interfaces;

/** Service contract for sending transactional emails. */
interface IEmailServ
{
    /** Sends a registration confirmation email.
     * @param string $toEmail Recipient email address
     * @param string $toName Recipient display name
     * @return bool True when email was sent successfully; otherwise false
     */
    public function sendRegistrationConfirmation(string $toEmail, string $toName): bool;

    /** Sends a password reset email containing a reset URL.
     * @param string $toEmail Recipient email address
     * @param string $toName Recipient display name
     * @param string $resetUrl Absolute password reset URL
     * @return bool True when email was sent successfully; otherwise false
     */
    public function sendPasswordReset(string $toEmail, string $toName, string $resetUrl): bool;

    /** Sends an order confirmation email.
     * @param string $toEmail Recipient email address
     * @param string $toName Recipient display name
     * @param string $subject Email subject
     * @param string $body HTML email body
     * @return bool True when email was sent successfully; otherwise false
     */
    public function sendOrderConfirmation(string $toEmail, string $toName, string $subject, string $body): bool;
}