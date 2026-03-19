<?php

namespace app\repositories\interfaces;

use app\dto\UserAuthDto;
use app\dto\UserIdentityDto;
use app\repositories\exceptions\UserRepoExc;

interface IUserRepo
{
    //region User Retrieval
    /** Retrieve a user by their email, returns User model or null if not found
     * @return UserAuthDto|null User authentication data or null if not found
     * @throws UserRepoExc if database query fails
     */
    public function getByEmail(string $email): ?UserAuthDto;

    /** Checks if a user with the given username already exists.
     * @return bool true if exists, false otherwise
     * @throws UserRepoExc if database query fails
     */
    public function existsByUsername(string $username): bool;

    /** Checks if a user with the given email already exists.
     * @return bool true if exists, false otherwise
     * @throws UserRepoExc if database query fails
     */
    public function existsByEmail(string $email): bool;

    /** Retrieve a user by their id
     * @return UserIdentityDto|null
     * @throws UserRepoExc if database query fails
     */
    public function findUserIdentityById(int $id): ?UserIdentityDto;
    //endregion


    //region Modification
    /** Create a new user in the database, after the service has validated the data
     * @return UserIdentityDto|null Created user's identity data, or null on failure
     * @throws UserRepoExc if database operation fails
     */
    public function createUser(string $firstName, string $lastName, string $username, string $email, ?string $phoneNumber, string $passwordHash, string $streetName, string $streetNumber, ?string $apartmentSuite, string $city, string $postalCode): ?UserIdentityDto;

    /** Update a user's data by their id
     * @param UserIdentityDto $user The user identity data to update
     * @param string|null $newPasswordHash Optional new password hash if password is being changed
     * @throws UserRepoExc if user not found or database operation fails
     */
    public function updateUser(UserIdentityDto $user, ?string $newPasswordHash = null): void;

    /** Delete user by their id
     * @throws UserRepoExc if user not found or database operation fails
     */
    public function deleteUser(int $id): void;

    /** Store a hashed password reset token with expiration.
     * @throws UserRepoExc if database operation fails
     */
    public function storePasswordResetToken(int $userId, string $tokenHash, string $expiresAt): void;

    /** Resolve a user id for a password reset token (valid/active tokens only).
     * @return int|null User id if token is valid, otherwise null.
     * @throws UserRepoExc if database operation fails
     */
    public function getUserIdByPasswordResetToken(string $tokenHash): ?int;

    /** Mark an active token as used.
     * @throws UserRepoExc if database operation fails
     */
    public function consumePasswordResetToken(string $tokenHash): void;

    /** Update only the password hash for a user id. Used exclusively during password reset operations.
     * @throws UserRepoExc if user not found or database operation fails
     */
    public function updatePasswordById(int $id, string $passwordHash): void;
    //endregion


    //region Admin User Management
    /** Get all users with optional pagination, search, and sorting
     * @param string|null $search Search by email or name
     * @param string $sortBy Column to sort by (email, name, role, created_at)
     * @param string $sortDir Sort direction (ASC or DESC)
     * @param int $limit Items per page
     * @param int $offset Pagination offset
     * @return array List of users with id, email, name, role, created_at, is_active
     */
    public function getAllUsers(?string $search = null, string $sortBy = 'created_at', string $sortDir = 'DESC', int $limit = 50, int $offset = 0): array;

    /** Get one user by id for admin management views/actions
     * @param int $id User ID
     * @return array|null User row or null if not found
     */
    public function findUserById(int $id): ?array;

    /** Search users with pagination and sorting
     * @param string|null $search Search by email, username, or name
     * @param string $sortBy Column to sort by (email, first_name, role, created_at)
     * @param string $sortDir Sort direction (ASC or DESC)
     * @param string $status Filter by status (all, active, banned)
     * @param int $limit Items per page
     * @param int $offset Pagination offset
     * @return array List of users with id, email, name, role, created_at, is_active
     */
    public function searchUsers(?string $search = null, string $sortBy = 'created_at', string $sortDir = 'DESC', string $status = 'all', int $limit = 50, int $offset = 0): array;

    /** Update user role by their id
     * @param int $id User ID
     * @param string $newRole New role value
     */
    public function updateUserRole(int $id, string $newRole): void;

    /** Edit user details (admin editing a user)
     * @param int $id User ID
     * @param string $firstName First name
     * @param string $lastName Last name
     * @param string $username Username
     * @param string $email Email address
     * @param string|null $phoneNumber Phone number (optional)
     * @param string $role User role
     */
    public function editUser(int $id, string $firstName, string $lastName, string $username, string $email, ?string $phoneNumber, string $role, string $streetName, string $streetNumber, string $city, string $postalCode, ?string $apartmentSuite = null): void;

    /** Deactivate (ban) user by their id
     * @param int $id User ID
     */
    public function deactivateUser(int $id): void;

    /** Reactivate (unban) user by their id
     * @param int $id User ID
     */
    public function reactivateUser(int $id): void;
    //endregion Admin User Management


    //region Admin Statistics
    /** Get total count of all registered users (including banned)
     * @return int Total registered user count
     */
    public function getTotalRegisteredUserCount(): int;

    /** Get total count of all active users (excluding banned)
     * @return int Active user count
     */
    public function getUserCount(): int;

    /** Get count of active orders (Open or Processing status)
     * @return int Active order count
     */
    public function getActiveOrderCount(): int;

    /** Get total revenue for the current month
     * @return float Total revenue
     */
    public function getMonthlyRevenue(): float;
    //endregion Admin Statistics


    //region Admin Content Management
    /** Get homepage content by page name
     * @param string $pageName Name of the page (e.g., 'homepage')
     * @return string|null HTML content or null if not found
     */
    public function getPageContent(string $pageName): ?string;

    /** Update homepage content for a specific page
     * @param string $pageName Name of the page (e.g., 'homepage')
     * @param string $content HTML content to save
     * @return void
     */
    public function updatePageContent(string $pageName, string $content): void;
    //endregion Admin Content Management
}