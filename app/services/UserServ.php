<?php

namespace app\services;

use app\dto\UserIdentityDto;
use app\models\Address;
use app\repositories\interfaces\IUserRepo;
use app\services\exceptions\BaseServExc;
use app\services\exceptions\UserServExc;
use app\services\exceptions\ValidationServExc;
use app\services\interfaces\IEmailServ;
use app\services\interfaces\IPasswordResetServ;
use app\services\interfaces\IUserServ;

final readonly class UserServ implements IUserServ
{
    private IUserRepo $userRepo;
    private ReCaptchaServ $recaptchaServ;
    private IEmailServ $emailServ;
    private IPasswordResetServ $passwordResetServ;

    public function __construct(IUserRepo $userRepo, ReCaptchaServ $recaptchaServ, IEmailServ $emailServ, IPasswordResetServ $passwordResetServ)
    {
        $this->userRepo = $userRepo;
        $this->recaptchaServ = $recaptchaServ;
        $this->emailServ = $emailServ;
        $this->passwordResetServ = $passwordResetServ;
    }

    //region Auth
    public function login(string $email, string $password): void
    {
        // Retrieve user auth data by email (id and password only)
        $auth = BaseServExc::handleRepoCall(
            fn() => $this->userRepo->getByEmail($email),
            UserServExc::class,
            __FUNCTION__
        );

        // Verify password
        if ($auth === null || !password_verify($password, $auth->passwordHash))
            throw new UserServExc(UserServExc::INVALID_CREDENTIALS);

        // Upon successful authentication, retrieve full user identity and set session data
        $identity = BaseServExc::handleRepoCall(
            fn() => $this->userRepo->findUserIdentityById($auth->id),
            UserServExc::class,
            __FUNCTION__
        );

        $this->setSessionData($identity);
    }

    public function logout(): void
    {
        // Only unset auth session data, regen session ID for CSRF protection.
        unset($_SESSION['auth']);
        session_regenerate_id(true);
    }

    public function signup(string $firstName, string $lastName, string $username, string $email, ?string $phoneNumber, string $password, string $passwordConfirm, string $streetName, string $streetNumber, ?string $apartmentSuite, string $city, string $postalCode, string $recaptchaToken): void
    {
        $this->validateIsNotEmpty($firstName, $lastName, $username, $email, $streetName, $streetNumber, $city, $postalCode, $password, $passwordConfirm);
        $this->sanitizeAccInput($firstName, $lastName, $username, $email, $phoneNumber, $streetName, $streetNumber, $apartmentSuite, $city, $postalCode);

        $this->validatePassword($password, $passwordConfirm);
        $this->recaptchaServ->validateToken($recaptchaToken);

        $this->validateAccInput($firstName, $lastName, $username, $email, $phoneNumber, $streetName, $streetNumber, $apartmentSuite, $city, $postalCode);
        $this->validateNonDuplicate($username, $email);

        $identity = BaseServExc::handleRepoCall(
            fn() => $this->userRepo->createUser($firstName, $lastName, $username, $email, $phoneNumber, password_hash($password, PASSWORD_DEFAULT), $streetName, $streetNumber, $apartmentSuite, $city, $postalCode),
            ValidationServExc::class,
            __FUNCTION__
        );
        if ($identity === null)
            throw new ValidationServExc(ValidationServExc::REGISTRATION_FAILED);

        // Successful registration, log in the new user
        $this->setSessionData($identity);

        $emailSent = $this->emailServ->sendRegistrationConfirmation($identity->email, $identity->firstName);
        if (!$emailSent)
            $_SESSION['flash_info'][] = 'Your account was created, but the confirmation email could not be sent.';
        else
            $_SESSION['flash_info'][] = "Welcome {$identity->firstName}! A confirmation email has been sent.";
    }

    public function requestPasswordReset(string $email): void
    {
        $this->passwordResetServ->request($email);
    }

    public function resetPassword(string $token, string $password, string $passwordConfirm): void
    {
        $this->passwordResetServ->resetPassword($token, $password, $passwordConfirm);
    }
    //endregion Auth


    //region User
    public function editAccount(string $firstName, string $lastName, string $username, string $email, ?string $phoneNumber, ?string $password, ?string $passwordConfirm, string $streetName, string $streetNumber, ?string $apartmentSuite, string $city, string $postalCode): void
    {
        $this->sanitizeAccInput($firstName, $lastName, $username, $email, $phoneNumber, $streetName, $streetNumber, $apartmentSuite, $city, $postalCode);

        // Validate password (only if new password is provided)
        $newPassword = null;
        if (!empty($password) || !empty($passwordConfirm)) {
            $this->validateIsNotEmpty($firstName, $lastName, $username, $email, $streetName, $streetNumber, $city, $postalCode, $password, $passwordConfirm);
            $this->validatePassword($password, $passwordConfirm);
            $newPassword = password_hash($password, PASSWORD_DEFAULT);
        } else
            $this->validateIsNotEmpty($firstName, $lastName, $username, $email, $streetName, $streetNumber, $city, $postalCode);

        $this->validateAccInput($firstName, $lastName, $username, $email, $phoneNumber, $streetName, $streetNumber, $apartmentSuite, $city, $postalCode);
        $this->validateNonDuplicate($username, $email, $_SESSION['auth']['username'], $_SESSION['auth']['email']);

        $updatedAddress = new Address(
            (int)($_SESSION['auth']['address']->addressId),
            $streetName,
            $streetNumber,
            $apartmentSuite,
            $city,
            $postalCode
        );

        $updatedIdentity = new UserIdentityDto(
            (int)$_SESSION['auth']['id'],
            $firstName,
            $lastName,
            $username,
            $email,
            $phoneNumber,
            $updatedAddress,
            $_SESSION['auth']['role']
        );

        // Update user in DB
        BaseServExc::handleRepoCall(
            fn() => $this->userRepo->updateUser($updatedIdentity, $newPassword),
            ValidationServExc::class,
            __FUNCTION__
        );

        // Set updated identity in session
        $this->setSessionData($updatedIdentity);
    }

    public function deleteAccount(string $confirmName): void
    {
        $username = $_SESSION['auth']['username'];

        if (!isset($confirmName))
            throw new UserServExc(UserServExc::DELETION_REQUIRES_CONFIRMATION);
        if ($confirmName !== $username)
            throw new UserServExc(UserServExc::DELETION_NAME_MISMATCH);

        // Hard delete the user
        BaseServExc::handleRepoCall(
            fn() => $this->userRepo->deleteUser($_SESSION['auth']['id']),
            UserServExc::class,
            __FUNCTION__
        );

        // Clear session data
        unset($_SESSION['auth']);
        session_regenerate_id(true);
    }
    //endregion User


    //region Admin Dashboard Statistics
    public function getTotalRegisteredUserCount(): int
    {
        return BaseServExc::handleRepoCall(
            fn() => $this->userRepo->getTotalRegisteredUserCount(),
            BaseServExc::class,
            __FUNCTION__
        );
    }

    public function getTotalUserCount(): int
    {
        return BaseServExc::handleRepoCall(
            fn() => $this->userRepo->getUserCount(),
            BaseServExc::class,
            __FUNCTION__
        );
    }

    public function getActiveOrderCount(): int
    {
        return BaseServExc::handleRepoCall(
            fn() => $this->userRepo->getActiveOrderCount(),
            BaseServExc::class,
            __FUNCTION__
        );
    }

    public function getMonthlyRevenue(): float
    {
        return BaseServExc::handleRepoCall(
            fn() => $this->userRepo->getMonthlyRevenue(),
            BaseServExc::class,
            __FUNCTION__
        );
    }
    //endregion Admin Dashboard Statistics


    //region Admin User Management
    public function getAllUsers(int $limit = 50, int $offset = 0): array
    {
        return BaseServExc::handleRepoCall(
            fn() => $this->userRepo->getAllUsers(null, 'created_at', 'DESC', $limit, $offset),
            BaseServExc::class,
            __FUNCTION__
        );
    }

    public function getUserById(int $id): ?array
    {
        return BaseServExc::handleRepoCall(
            fn() => $this->userRepo->findUserById($id),
            BaseServExc::class,
            __FUNCTION__
        );
    }

    public function searchUsers(?string $search = null, string $sortBy = 'created_at', string $sortDir = 'DESC', string $status = 'all'): array
    {
        return BaseServExc::handleRepoCall(
            fn() => $this->userRepo->searchUsers($search, $sortBy, $sortDir, $status, 50, 0),
            BaseServExc::class,
            __FUNCTION__
        );
    }

    public function deactivateUser(int $id): void
    {
        BaseServExc::handleRepoCall(
            fn() => $this->userRepo->deactivateUser($id),
            BaseServExc::class,
            __FUNCTION__
        );
    }

    public function reactivateUser(int $id): void
    {
        BaseServExc::handleRepoCall(
            fn() => $this->userRepo->reactivateUser($id),
            BaseServExc::class,
            __FUNCTION__
        );
    }

    public function editUser(int $id, string $firstName, string $lastName, string $username, string $email, string $role, string $streetName, string $streetNumber, string $city, string $postalCode, ?string $phoneNumber, ?string $apartmentSuite): void
    {
        $currentUser = $this->getUserById($id);
        if (!$currentUser)
            throw new ValidationServExc(ValidationServExc::USER_NOT_FOUND);

        $this->validateIsNotEmpty($firstName, $lastName, $username, $email, $streetName, $streetNumber, $city, $postalCode);
        $this->sanitizeAccInput($firstName, $lastName, $username, $email, $phoneNumber, $streetName, $streetNumber, $apartmentSuite, $city, $postalCode);

        $this->validateAccInput($firstName, $lastName, $username, $email, $phoneNumber, $streetName, $streetNumber, $apartmentSuite, $city, $postalCode);
        $this->validateNonDuplicate(
            $username,
            $email,
            $currentUser['username'] ?? null,
            $currentUser['email'] ?? null
        );

        BaseServExc::handleRepoCall(
            fn() => $this->userRepo->editUser($id, $firstName, $lastName, $username, $email, $phoneNumber, $role, $streetName, $streetNumber, $city, $postalCode, $apartmentSuite),
            BaseServExc::class,
            __FUNCTION__
        );
    }
    //endregion Admin User Management


    //region Admin Homepage Content Management
    public function getPageContent(string $pageName = 'homepage'): string
    {
        $content = BaseServExc::handleRepoCall(
            fn() => $this->userRepo->getPageContent($pageName),
            BaseServExc::class,
            __FUNCTION__
        );

        return $content ?? '';
    }

    public function updatePageContent(string $pageName, string $content): void
    {
        if (empty(trim($content)))
            throw new ValidationServExc(ValidationServExc::CONTENT_CANNOT_BE_EMPTY);

        $allowedTags = '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><img><div><span>';
        $sanitizedContent = strip_tags($content, $allowedTags);

        BaseServExc::handleRepoCall(
            fn() => $this->userRepo->updatePageContent($pageName, $sanitizedContent),
            BaseServExc::class,
            __FUNCTION__
        );
    }
    //endregion Admin Homepage Content Management


    //region Router
    public function validateAccessRole($routeReqRole): void
    {
        $userRole = $_SESSION['auth']['role'] ?? null;
        if ($routeReqRole->value > $userRole->toAccessRole()->value)
            throw new UserServExc(UserServExc::INSUFFICIENT_PERMISSIONS);
    }

    public function redirectWhenLoggedIn(string $routeName): void
    {
        if (($routeName === 'loginPage' || $routeName === 'signupPage') && isset($_SESSION['auth']['id']))
            throw new UserServExc(UserServExc::ALREADY_LOGGED_IN);
    }
    //endregion Router


    //region Private Helper Methods
    /** Sanitizes and normalizes user input fields */
    private function sanitizeAccInput(string &$firstName, string &$lastName, string &$username, string &$email, ?string &$phoneNumber, string &$streetName, string &$streetNumber, ?string &$apartmentSuite, string &$city, string &$postalCode): void
    {
        $firstName = ucfirst(strtolower(trim($firstName)));
        $lastName = ucfirst(strtolower(trim($lastName)));
        $username = trim($username);
        $email = strtolower(trim($email));
        $phoneNumber = !empty($phoneNumber) ? trim($phoneNumber) : null; // OPTIONAL

        $streetName = trim($streetName);
        $streetNumber = trim($streetNumber);
        $apartmentSuite = !empty($apartmentSuite) ? trim($apartmentSuite) : null; // OPTIONAL
        $city = ucwords(strtolower(trim($city)));
        $postalCode = strtoupper(trim($postalCode));
    }

    /** Validates required fields are not empty */
    private function validateIsNotEmpty(string $firstName, string $lastName, string $username, string $email, string $streetName, string $streetNumber, string $city, string $postalCode, ?string $password = null, ?string $passwordConfirm = null): void
    {
        if (empty($firstName) || empty($lastName) || empty($username) || empty($email) || empty($streetName) || empty($streetNumber) || empty($city) || empty($postalCode))
            throw new ValidationServExc(ValidationServExc::FIELDS_REQUIRED);

        // Password check only required during signup (new password is optional with edit)
        if ($password !== null && trim($password) === '')
            throw new ValidationServExc(ValidationServExc::FIELDS_REQUIRED);
    }

    /** Validates password and confirmation match
     *  And validates password format */
    private function validatePassword(string $password, string $passwordConfirm): void
    {
        if ($password !== $passwordConfirm)
            throw new ValidationServExc(ValidationServExc::PASSWORD_MISMATCH);

        // REGEX: positive lookahead of at least: one lower, one upper, one digit. No spaces of length 12-64
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])\S{12,64}$/', $password))
            throw new ValidationServExc(ValidationServExc::PASSWORD_INVALID);
    }

    /** Validates all user input fields format */
    private function validateAccInput(string $firstName, string $lastName, string $username, string $email, ?string $phoneNumber, string $streetName, string $streetNumber, ?string $apartmentSuite, string $city, string $postalCode): void
    {
        // ACCOUNT
        // FirstName & LastName: alphabetic characters only
        if (!preg_match('/^[a-zA-Z]+$/', $firstName))
            throw new ValidationServExc(ValidationServExc::FIRSTNAME_INVALID);
        if (!preg_match('/^[a-zA-Z]+$/', $lastName))
            throw new ValidationServExc(ValidationServExc::LASTNAME_INVALID);

        // Username: 3-32 characters, alphanumeric + underscore)
        if (!preg_match('/^[a-zA-Z0-9_]{3,32}$/', $username))
            throw new ValidationServExc(ValidationServExc::USERNAME_INVALID);

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new ValidationServExc(ValidationServExc::EMAIL_INVALID);


        // ADDRESS
        // Street name: letters, spaces, dots, dashes - max 100 chars
        if (!preg_match('/^[A-Za-z\s.-]{1,100}$/', $streetName))
            throw new ValidationServExc(ValidationServExc::ADDRESS_STREET_INVALID);

        // Street number: 1-5 digits, optionally followed by a letter suffix (e.g. 12A)
        if (!preg_match('/^[0-9]{1,5}[a-zA-Z]?$/', $streetNumber))
            throw new ValidationServExc(ValidationServExc::ADDRESS_STREET_NUMBER_INVALID);

        // City: letters, spaces, dashes - max 100 chars
        if (!preg_match('/^[A-Za-z\s.-]{1,100}$/', $city))
            throw new ValidationServExc(ValidationServExc::ADDRESS_CITY_INVALID);

        // Dutch postal code: 4 digits, 2 letters (Sanitized before to be all Upper without spaces)
        if (!preg_match('/^[0-9]{4}[A-Z]{2}$/', $postalCode))
            throw new ValidationServExc(ValidationServExc::ADDRESS_POSTAL_CODE_INVALID);


        // OPTIONAL
        // Phonenumber: starts with + followed by 1-3 digit country code, optional space or dash, then 8-15 digit phone number
        if (!empty($phoneNumber) && !preg_match('/^\+[0-9]{1,3}[\s\-]?[0-9]{8,15}$/', $phoneNumber))
            throw new ValidationServExc(ValidationServExc::PHONE_NUMBER_INVALID);

        // Apartment / suite: alphanumeric + spaces/dashes - max 20 chars
        if (!empty($apartmentSuite) && !preg_match('/^[A-Za-z0-9\s\-]{1,20}$/', $apartmentSuite))
            throw new ValidationServExc(ValidationServExc::ADDRESS_APARTMENT_INVALID);
    }

    /** Checks if username and/or email are already taken in the database */
    private function validateNonDuplicate(string $username, string $email, ?string $currentUsername = null, ?string $currentEmail = null): void
    {
        // Only check username if it's new or changed
        if ($currentUsername === null || $username !== $currentUsername) {
            $usernameExists = BaseServExc::handleRepoCall(
                fn() => $this->userRepo->existsByUsername($username),
                ValidationServExc::class,
                'validateAvailability'
            );
            if ($usernameExists)
                throw new ValidationServExc(ValidationServExc::USERNAME_TAKEN);
        }

        // Only check email if it's new or changed
        if ($currentEmail === null || $email !== $currentEmail) {
            $emailExists = BaseServExc::handleRepoCall(
                fn() => $this->userRepo->existsByEmail($email),
                ValidationServExc::class,
                'validateAvailability'
            );
            if ($emailExists)
                throw new ValidationServExc(ValidationServExc::EMAIL_TAKEN);
        }
    }

    /** Sets session auth data for logged in or newly registered user */
    private function setSessionData(UserIdentityDto $user): void
    {
        session_regenerate_id(true);
        $_SESSION['auth'] = [
            'id' => $user->id,
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'username' => $user->username,
            'email' => $user->email,
            'phoneNumber' => $user->phoneNumber,
            'address' => $user->address,
            'role' => $user->role,
            'ts' => time() // Currently not used for session expiration
        ];
    }

    //endregion Private Helper Methods
}