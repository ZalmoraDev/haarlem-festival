<?php

namespace app\services\exceptions;

/** Used for login, registration & general user input validation.
 * Doesn't require access to repository layers, as this exception is only to validate input from the view/controller*/
final class ValidationServExc extends BaseServExc
{
    public const string FIELDS_REQUIRED = 'All fields are required,<br>please try again.';

    public const string USER_NOT_FOUND = 'User not found.';

    public const string FIRSTNAME_INVALID = 'Your first name must only contain letters,<br>please try again.';
    public const string LASTNAME_INVALID = 'Your last name must only contain letters,<br>please try again.';

    public const string USERNAME_INVALID = 'Username must be 3-32 characters (letters, numbers, underscore only).';
    public const string USERNAME_TAKEN = 'Your provided username is already taken,<br>please try again.';

    public const string EMAIL_INVALID = 'Your provided email is not valid,<br>please try again.';
    public const string EMAIL_TAKEN = 'Your provided email is already taken,<br>please try again.';
    public const string PHONE_NUMBER_INVALID = 'Your provided phone number is not valid,<br>please try again.';
    public const string PASSWORD_INVALID = 'Your password must be in the following format:<br>at least one lowercase, one uppercase, one digit, no spaces<br>length of 12-64,<br>please try again.';
    public const string PASSWORD_MISMATCH = 'Your passwords did not match,<br>please try again.';

    public const string ADDRESS_STREET_INVALID = 'The provided street name is not valid,<br>please try again.';
    public const string ADDRESS_STREET_NUMBER_INVALID = 'The provided street number is not valid (numbers only),<br>please try again.';
    public const string ADDRESS_APARTMENT_INVALID = 'The provided apartment/suite is not valid,<br>please try again.';
    public const string ADDRESS_CITY_INVALID = 'The provided city name is not valid,<br>please try again.';
    public const string ADDRESS_POSTAL_CODE_INVALID = 'The provided postal code is not valid (e.g. 1234AB),<br>please try again.';

    public const string RECAPTCHA_REQUIRED = 'Please verify that you are not a robot.';
    public const string RECAPTCHA_INVALID = 'ReCaptcha verification failed,<br>please try again.';
    public const string RECAPTCHA_FAILED = 'ReCaptcha verification error,<br>please try again later.';
    
    public const string REGISTRATION_FAILED = 'Registration failed due to a server error,<br>please try again later.';
    
    public const string CONTENT_CANNOT_BE_EMPTY = 'Content cannot be empty.';
}