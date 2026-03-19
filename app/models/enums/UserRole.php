<?php

namespace app\models\enums;

/** User role stored with the user, used in displaying role and in access comparison through AccessRole */
enum UserRole: string
{
    case Visitor = 'Visitor'; // Default role, set for each initialized session in index.php
    case Customer = 'Customer';
    case Validated = 'Validated'; // Role for users that have validated their email.
    case Employee = 'Employee';
    case Admin = 'Admin';

    /** Translate UserRole string to AccessRole int for access comparison in IUserServ::verifyByAccessRole() */
    public function toAccessRole(): AccessRole
    {
        return match ($this) {
            self::Visitor => AccessRole::Visitor,
            self::Customer => AccessRole::Customer,
            self::Validated => AccessRole::Validated,
            self::Employee => AccessRole::Employee,
            self::Admin => AccessRole::Admin
        };
    }
}