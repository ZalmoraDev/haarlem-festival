<?php

namespace app\models;

use app\models\enums\UserRole;
use DateTimeImmutable;

/** 1:1 mapping to 'users' DB table (only derived DTO's should be used, UserAuthDTO for auth, UserProfileDTO everything else) */
final readonly class User
{
    public function __construct(
        public int               $id,
        public string            $firstName,
        public string            $lastName,
        public string            $username, // Username doesn't make sense since we already use first- & lastname, but it's in the casus
        public string            $email,
        public ?string           $phoneNumber, // Optional, also never used besides storing in DB
        public string            $passwordHash,

        public Address  $address,

        public UserRole          $role,
        public DateTimeImmutable $createdAt
    )
    {
    }
}