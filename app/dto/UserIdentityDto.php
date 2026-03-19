<?php

namespace app\dto;

use app\models\Address;
use app\models\enums\UserRole;

/**
 * BASE: User model
 *
 * Used for identifying user information without sensitive data (password).
 * For password, use UserAuthDto.php
 */
final readonly class UserIdentityDto
{
    public function __construct(
        public int      $id,
        public string   $firstName,
        public string   $lastName,
        public string   $username,
        public string   $email,
        public ?string  $phoneNumber, // Optional, also never used besides storing in DB

        public Address  $address,

        public UserRole $role
    )
    {
    }
}