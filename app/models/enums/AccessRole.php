<?php

namespace app\models\enums;

/** Used in Router comparisons for access control.
 * Represents UserRole roles as integers instead of strings, to validate acces based on if the User's Role is the required role OR higher
 * So an Admin can access Employee routes, but an Employee cannot access Admin routes.
 * */
enum AccessRole: int
{
    case Visitor = 1; // Default role for set by initialized session in index.php
    case Customer = 2;
    case Validated = 3; // Role for users that have validated their email.
    case Employee = 4;
    case Admin = 5;
    // TODO: maybe create a new role with more privileges than a regular Admin, which can demote/promote Admins to and from Employees?
}