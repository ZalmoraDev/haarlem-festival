<?php

namespace app\services;

use app\repositories\interfaces\IUserRepo;
use app\services\exceptions\BaseServExc;
use app\services\exceptions\UserServExc;
use app\services\exceptions\ValidationServExc;
use app\services\interfaces\IEmailServ;
use app\services\interfaces\IPasswordResetServ;
use DateTimeImmutable;

final readonly class PasswordResetServ implements IPasswordResetServ
{
    private IUserRepo $userRepo;
    private IEmailServ $emailServ;

    public function __construct(IUserRepo $userRepo, IEmailServ $emailServ)
    {
        $this->userRepo = $userRepo;
        $this->emailServ = $emailServ;
    }

    public function request(string $email): void
    {
        $email = strtolower(trim($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new ValidationServExc(ValidationServExc::EMAIL_INVALID);

        $auth = BaseServExc::handleRepoCall(
            fn() => $this->userRepo->getByEmail($email),
            UserServExc::class,
            __FUNCTION__
        );

        if ($auth === null)
            return;

        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = (new DateTimeImmutable('+1 hour'))->format('Y-m-d H:i:sP');

        BaseServExc::handleRepoCall(
            fn() => $this->userRepo->storePasswordResetToken($auth->id, $tokenHash, $expiresAt),
            UserServExc::class,
            __FUNCTION__
        );

        $identity = BaseServExc::handleRepoCall(
            fn() => $this->userRepo->findUserIdentityById($auth->id),
            UserServExc::class,
            __FUNCTION__
        );

        if ($identity === null)
            return;

        $this->emailServ->sendPasswordReset(
            $identity->email,
            $identity->firstName,
            $this->buildResetUrl($token)
        );
    }

    public function resetPassword(string $resetToken, string $password, string $passwordConfirm): void
    {
        $resetToken = strtolower(trim($resetToken));
        if (!preg_match('/^[a-f0-9]{64}$/', $resetToken))
            throw new UserServExc(UserServExc::PASSWORD_RESET_LINK_INVALID);

        if (empty($password) || empty($passwordConfirm))
            throw new ValidationServExc(ValidationServExc::FIELDS_REQUIRED);

        if ($password !== $passwordConfirm)
            throw new ValidationServExc(ValidationServExc::PASSWORD_MISMATCH);

        // Password REGEX: at least one lower, one upper, one digit, no spaces, length 12-64
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])\S{12,64}$/', $password))
            throw new ValidationServExc(ValidationServExc::PASSWORD_INVALID);

        $tokenHash = hash('sha256', $resetToken);
        $userId = BaseServExc::handleRepoCall(
            fn() => $this->userRepo->getUserIdByPasswordResetToken($tokenHash),
            UserServExc::class,
            __FUNCTION__
        );

        if ($userId === null)
            throw new UserServExc(UserServExc::PASSWORD_RESET_LINK_INVALID);

        BaseServExc::handleRepoCall(
            fn() => $this->userRepo->updatePasswordById($userId, password_hash($password, PASSWORD_DEFAULT)),
            UserServExc::class,
            __FUNCTION__
        );

        BaseServExc::handleRepoCall(
            fn() => $this->userRepo->consumePasswordResetToken($tokenHash),
            UserServExc::class,
            __FUNCTION__
        );
    }

    private function buildResetUrl(string $token): string
    {
        $baseUrl = rtrim($_ENV['SITE_URL'] ?? '', '/');

        if ($baseUrl === '') {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $baseUrl = $protocol . '://' . $host;
        }

        return $baseUrl . '/reset-password?token=' . urlencode($token);
    }
}
