<?php

namespace app\services\interfaces;

use app\models\enums\UserRole;
use app\services\exceptions\UserServExc;
use app\services\exceptions\ValidationServExc;

interface IUserServ
{
    //region Auth
    /** Attempts to log in a user with provided credentials.
     * @param string $email User's email address
     * @param string $password User's password
     * @throws UserServExc if credentials are invalid.
     */
    public function login(string $email, string $password): void;

    /** Logs out by unsetting session auth data */
    public function logout(): void;

    /** Attempts to register a new user with provided data.
     * @throws ValidationServExc if any validation fails.
     */
    public function signup(string $firstName, string $lastName, string $username, string $email, ?string $phoneNumber, string $password, string $passwordConfirm, string $streetName, string $streetNumber, ?string $apartmentSuite, string $city, string $postalCode, string $recaptchaToken): void;

    /** Starts the password reset flow by email.
     * @param string $email User's email address
     * @throws ValidationServExc if email format is invalid.
     */
    public function requestPasswordReset(string $email): void;

    /** Completes the password reset flow for a valid token.
     * @param string $token Raw reset token from email link
     * @param string $password New password
     * @param string $passwordConfirm New password confirmation
     * @throws ValidationServExc if password validation fails.
     * @throws UserServExc if token is invalid or expired.
     */
    public function resetPassword(string $token, string $password, string $passwordConfirm): void;
    //endregion


    //region User
    /** Edit the logged-in user's account details
     * @throws ValidationServExc if validation fails
     */
    public function editAccount(string $firstName, string $lastName, string $username, string $email, ?string $phoneNumber, ?string $password, ?string $passwordConfirm, string $streetName, string $streetNumber, ?string $apartmentSuite, string $city, string $postalCode): void;

    /** Delete the logged-in user's account
     * @param string $confirmName Username confirmation for deletion
     * @throws UserServExc if deletion fails
     */
    public function deleteAccount(string $confirmName): void;
    //endregion User


    //region Admin Dashboard Statistics
    /** Get total count of all registered users (including banned)
     * @return int Total number of registered users
     */
    public function getTotalRegisteredUserCount(): int;

    /** Get total count of active users (excluding banned)
     * @return int Total number of active users
     */
    public function getTotalUserCount(): int;

    /** Get count of active orders
     * @return int Number of pending/open orders
     */
    public function getActiveOrderCount(): int;

    /** Get total revenue for the current month
     * @return float Total revenue in euros
     */
    public function getMonthlyRevenue(): float;
    //endregion Admin Dashboard Statistics


    //region Admin User Management
    /** Get all registered users with pagination
     * @param int $limit Number of users per page
     * @param int $offset Pagination offset
     * @return array List of users
     */
    public function getAllUsers(int $limit = 50, int $offset = 0): array;

    /** Get a specific user by ID
     * @param int $id User ID
     * @return array|null User data or null if not found
     */
    public function getUserById(int $id): ?array;

    /** Search and sort users
     * @param string|null $search Search query (email or name)
     * @param string $sortBy Column to sort by (email, name, role, created_at)
     * @param string $sortDir Sort direction (ASC or DESC)
     * @param string $status Filter by status (all, active, banned)
     * @return array List of matching users
     */
    public function searchUsers(?string $search = null, string $sortBy = 'created_at', string $sortDir = 'DESC', string $status = 'all'): array;

    /** Ban (deactivate) a user by ID
     * @param int $id User ID to deactivate
     */
    public function deactivateUser(int $id): void;

    /** Unban (reactivate) a user by ID
     * @param int $id User ID to reactivate
     */
    public function reactivateUser(int $id): void;

    /** Edit user details (admin editing a user)
     * @param int $id User ID
     * @param string $firstName First name
     * @param string $lastName Last name
     * @param string $username Username
     * @param string $email Email address
     * @param string|null $phoneNumber Phone number (optional)
     * @param string $role User role
     */
    public function editUser(int $id, string $firstName, string $lastName, string $username, string $email, string $role, string $streetName, string $streetNumber, string $city, string $postalCode, ?string $phoneNumber, ?string $apartmentSuite): void;
    //endregion Admin User Management


    //region Admin Homepage Content Management
    /** Get homepage content
     * @param string $pageName Name of the page (default: 'homepage')
     * @return string HTML content (empty string if not found)
     */
    public function getPageContent(string $pageName = 'homepage'): string;

    /** Update homepage content
     * @param string $pageName Name of the page
     * @param string $content HTML content to save (will be sanitized)
     * @return void
     */
    public function updatePageContent(string $pageName, string $content): void;
    //endregion Admin Homepage Content Management


    //region Router
    /** Checks if the current user has the correct UserRole compared to required AccesRole the route requires.
     * @param UserRole $routeReqRole
     * @throws UserServExc if route requires authentication but user is not authenticated
     */
    public function validateAccessRole(UserRole $routeReqRole): void;

    /** Checks if user is already logged in when accessing login/signup pages
     * @param string $routeName The name of the route being accessed
     * @throws UserServExc if user is already logged in and tries to access login/signup pages
     */
    public function redirectWhenLoggedIn(string $routeName): void;
    //endregion
}