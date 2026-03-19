<?php

namespace app\repositories;

use app\dto\UserAuthDto;
use app\dto\UserIdentityDto;
use app\models\Address;
use app\models\enums\UserRole;
use app\repositories\exceptions\UserRepoExc;
use app\repositories\interfaces\IUserRepo;
use PDO;
use PDOException;


final class UserRepo extends BaseRepository implements IUserRepo
{
    //region Retrieval
    //region User
    public function getByEmail(string $email): ?UserAuthDto
    {
        try {
            $stmt = $this->connection->prepare('
                    SELECT *
                    FROM users
                    WHERE email = :email AND is_active = TRUE;'
            );

            $stmt->execute([
                'email' => $email
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? new UserAuthDto(
                $row['id'],
                $row['password_hash']
            ) : null;
        } catch (PDOException $e) {
            error_log("Database error in findAuthByEmail: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_FETCH_USER);
        }
    }

    public function existsByUsername(string $username): bool
    {
        try {
            $stmt = $this->connection->prepare('
            SELECT EXISTS (
                SELECT 1
                FROM users
                WHERE username = :name
            )');

            $stmt->execute(['name' => $username]);

            return (bool)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database error in existsByUsername: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_CHECK_USERNAME);
        }
    }

    public function existsByEmail(string $email): bool
    {
        try {
            $stmt = $this->connection->prepare('
            SELECT EXISTS (
                SELECT 1
                FROM users
                WHERE email = :email
            )');

            $stmt->execute(['email' => $email]);

            return (bool)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database error in existsByEmail: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_CHECK_EMAIL);
        }
    }

    public function findUserIdentityById(int $id): ?UserIdentityDto
    {
        try {
            $stmt = $this->connection->prepare('
                    SELECT u.id as user_id, u.first_name, u.last_name, u.username, u.email, u.phone_number, u.role,
                           a.id as address_id, a.street_name, a.street_number, a.apartment_suite, a.city, a.postal_code
                    FROM users u
                    LEFT JOIN addresses a ON u.address_id = a.id
                    WHERE u.id = :id AND u.is_active = TRUE'
            );

            $stmt->execute([
                'id' => $id
            ]);

            // 1) User doesn't exist or is soft-deleted (is_active = FALSE)
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row)
                return null;

            $address = new Address(
                (int)($row['address_id'] ?? 0),
                $row['street_name'] ?? '',
                (int)($row['street_number'] ?? 0),
                $row['apartment_suite'],
                $row['city'] ?? '',
                $row['postal_code'] ?? ''
            );

            return new UserIdentityDto(
                (int)$row['user_id'],
                $row['first_name'],
                $row['last_name'],
                $row['username'],
                $row['email'],
                $row['phone_number'],
                $address,
                UserRole::from($row['role'])
            );
        } catch (PDOException $e) {
            error_log("Database error in findUserIdentityById: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_FETCH_USER);
        }
    }
    //endregion
    //endregion


    //region Modification
    public function createUser(string $firstName, string $lastName, string $username, string $email, ?string $phoneNumber, string $passwordHash, string $streetName, string $streetNumber, ?string $apartmentSuite, string $city, string $postalCode): ?UserIdentityDto
    {
        try {
            $this->connection->beginTransaction(); // Create Address + Create User (with address_id) atomic

            // 1.1) Create address
            $addressStmt = $this->connection->prepare('
                    INSERT INTO addresses (street_name, street_number, apartment_suite, city, postal_code)
                    VALUES (:street_name, :street_number, :apartment_suite, :city, :postal_code)
                    RETURNING id'
            );
            $addressStmt->execute([
                'street_name' => $streetName,
                'street_number' => $streetNumber,
                'apartment_suite' => $apartmentSuite,
                'city' => $city,
                'postal_code' => $postalCode,
            ]);

            // 1.2) Fetch address's ID to use within user creation
            $addressId = $addressStmt->fetchColumn();
            if (empty($addressId)) {
                $this->connection->rollBack();
                throw new UserRepoExc(UserRepoExc::FAILED_TO_CREATE_USER);
            }

            // 2.1) Create user
            $userStmt = $this->connection->prepare('
                    INSERT INTO users (first_name, last_name, username, email, phone_number, password_hash, address_id, role)
                    VALUES (:first_name, :last_name, :username, :email, :phone_number, :password_hash, :address_id, :role)
                    RETURNING id'
            );
            $userStmt->execute([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'username' => $username,
                'phone_number' => $phoneNumber,
                'email' => $email,
                'password_hash' => $passwordHash,
                'address_id' => (int)$addressId,
                'role' => UserRole::Customer->value
            ]);

            // 2.2) Fetch user's ID to verify if both address and user were created
            $userId = $userStmt->fetchColumn();
            if (empty($userId)) {
                $this->connection->rollBack();
                throw new UserRepoExc(UserRepoExc::FAILED_TO_CREATE_USER);
            }

            // 3.1) Commit to DB and return identity (All fields besides password)
            $this->connection->commit();
            $address = new Address(
                (int)$addressId,
                $streetName,
                $streetNumber,
                $apartmentSuite,
                $city,
                $postalCode
            );

            return new UserIdentityDto(
                (int)$userId,
                $firstName,
                $lastName,
                $username,
                $email,
                $phoneNumber,
                $address,
                UserRole::Customer
            );
        } catch (PDOException $e) {
            if ($this->connection->inTransaction())
                $this->connection->rollBack();

            error_log("Database error in createUser: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_CREATE_USER);
        }
    }

    public function updateUser(UserIdentityDto $user, ?string $newPasswordHash = null): void
    {
        try {
            $this->connection->beginTransaction();

            $addressIdStmt = $this->connection->prepare('
                SELECT address_id
                FROM users
                WHERE id = :id AND is_active = TRUE
                LIMIT 1
            ');
            $addressIdStmt->execute(['id' => $user->id]);
            $addressId = $addressIdStmt->fetchColumn();
            if ($addressId === false) {
                $this->connection->rollBack();
                throw new UserRepoExc(UserRepoExc::USER_NOT_FOUND);
            }

            if ($newPasswordHash !== null) {
                $stmt = $this->connection->prepare('
                    UPDATE users
                    SET first_name = :first_name, 
                        last_name = :last_name, 
                        username = :username, 
                        email = :email, 
                        phone_number = :phone_number, 
                        password_hash = :password_hash
                    WHERE id = :id
                ');

                $stmt->execute([
                    'id' => $user->id,
                    'first_name' => $user->firstName,
                    'last_name' => $user->lastName,
                    'username' => $user->username,
                    'email' => $user->email,
                    'phone_number' => $user->phoneNumber,
                    'password_hash' => $newPasswordHash,
                ]);
            } else {
                $stmt = $this->connection->prepare('
                    UPDATE users
                    SET first_name = :first_name, 
                        last_name = :last_name, 
                        username = :username, 
                        email = :email, 
                        phone_number = :phone_number
                    WHERE id = :id
                ');

                $stmt->execute([
                    'id' => $user->id,
                    'first_name' => $user->firstName,
                    'last_name' => $user->lastName,
                    'username' => $user->username,
                    'email' => $user->email,
                    'phone_number' => $user->phoneNumber
                ]);
            }

            if ($addressId !== null) {
                $addressStmt = $this->connection->prepare('
                    UPDATE addresses
                    SET street_name = :street_name,
                        street_number = :street_number,
                        apartment_suite = :apartment_suite,
                        city = :city,
                        postal_code = :postal_code
                    WHERE id = :address_id
                ');

                $addressStmt->execute([
                    'address_id' => (int)$addressId,
                    'street_name' => $user->address->streetAddress,
                    'street_number' => $user->address->streetNumber,
                    'apartment_suite' => $user->address->apartmentSuite,
                    'city' => $user->address->city,
                    'postal_code' => $user->address->postalCode,
                ]);
            } else {
                $newAddressStmt = $this->connection->prepare('
                    INSERT INTO addresses (street_name, street_number, apartment_suite, city, postal_code)
                    VALUES (:street_name, :street_number, :apartment_suite, :city, :postal_code)
                    RETURNING id
                ');

                $newAddressStmt->execute([
                    'street_name' => $user->address->streetAddress,
                    'street_number' => $user->address->streetNumber,
                    'apartment_suite' => $user->address->apartmentSuite,
                    'city' => $user->address->city,
                    'postal_code' => $user->address->postalCode,
                ]);

                $newAddressId = $newAddressStmt->fetchColumn();
                if ($newAddressId === false) {
                    $this->connection->rollBack();
                    throw new UserRepoExc(UserRepoExc::FAILED_TO_UPDATE_USER);
                }

                $linkStmt = $this->connection->prepare('UPDATE users SET address_id = :address_id WHERE id = :id');
                $linkStmt->execute([
                    'address_id' => (int)$newAddressId,
                    'id' => $user->id,
                ]);
            }

            $this->connection->commit();
        } catch (PDOException $e) {
            if ($this->connection->inTransaction())
                $this->connection->rollBack();

            error_log("Database error in updateUser: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_UPDATE_USER);
        }
    }

    public function deleteUser(int $id): void
    {
        try {
            $stmt = $this->connection->prepare('
                DELETE FROM users
                WHERE id = :id
            ');

            $stmt->execute([
                'id' => $id
            ]);

            if ($stmt->rowCount() === 0)
                throw new UserRepoExc(UserRepoExc::USER_NOT_FOUND);
        } catch (PDOException $e) {
            error_log("Database error in deleteUser: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_DELETE_USER);
        }
    }

    public function storePasswordResetToken(int $userId, string $tokenHash, string $expiresAt): void
    {
        try {
            $this->connection->beginTransaction();

            $invalidateStmt = $this->connection->prepare('
                UPDATE password_reset_tokens
                SET used_at = NOW()
                WHERE user_id = :user_id
                  AND used_at IS NULL
            ');

            $invalidateStmt->execute([
                'user_id' => $userId
            ]);

            $stmt = $this->connection->prepare('
                INSERT INTO password_reset_tokens (user_id, token_hash, expires_at)
                VALUES (:user_id, :token_hash, :expires_at)
            ');

            $stmt->execute([
                'user_id' => $userId,
                'token_hash' => $tokenHash,
                'expires_at' => $expiresAt
            ]);

            $this->connection->commit();
        } catch (PDOException $e) {
            if ($this->connection->inTransaction())
                $this->connection->rollBack();

            error_log("Database error in storePasswordResetToken: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_STORE_PASSWORD_RESET_TOKEN);
        }
    }

    public function getUserIdByPasswordResetToken(string $tokenHash): ?int
    {
        try {
            $stmt = $this->connection->prepare('
                SELECT user_id
                FROM password_reset_tokens
                WHERE token_hash = :token_hash
                  AND used_at IS NULL
                  AND expires_at > NOW()
                ORDER BY created_at DESC
                LIMIT 1
            ');

            $stmt->execute([
                'token_hash' => $tokenHash
            ]);

            $result = $stmt->fetchColumn();
            return $result === false ? null : (int)$result;
        } catch (PDOException $e) {
            error_log("Database error in getUserIdByPasswordResetToken: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_FETCH_PASSWORD_RESET_TOKEN);
        }
    }

    public function consumePasswordResetToken(string $tokenHash): void
    {
        try {
            $stmt = $this->connection->prepare('
                UPDATE password_reset_tokens
                SET used_at = NOW()
                WHERE token_hash = :token_hash
                  AND used_at IS NULL
            ');

            $stmt->execute([
                'token_hash' => $tokenHash
            ]);
        } catch (PDOException $e) {
            error_log("Database error in consumePasswordResetToken: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_CONSUME_PASSWORD_RESET_TOKEN);
        }
    }

    public function updatePasswordById(int $id, string $passwordHash): void
    {
        try {
            $stmt = $this->connection->prepare('
                UPDATE users
                SET password_hash = :password_hash
                WHERE id = :id
                  AND is_active = TRUE
            ');

            $stmt->execute([
                'id' => $id,
                'password_hash' => $passwordHash
            ]);

            if ($stmt->rowCount() === 0)
                throw new UserRepoExc(UserRepoExc::USER_NOT_FOUND);
        } catch (PDOException $e) {
            error_log("Database error in updatePasswordById: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_UPDATE_PASSWORD);
        }
    }

    public function getAllUsers(?string $search = null, string $sortBy = 'created_at', string $sortDir = 'DESC', int $limit = 50, int $offset = 0): array
    {
        try {
            $validSortBy = [
                'id' => 'u.id',
                'email' => 'u.email',
                'first_name' => 'u.first_name',
                'role' => 'u.role',
                'created_at' => 'u.created_at',
            ];
            $sortBy = $validSortBy[$sortBy] ?? 'u.created_at';
            $sortDir = strtoupper($sortDir) === 'ASC' ? 'ASC' : 'DESC';

            $sql = '
                SELECT u.id, u.email, u.first_name, u.last_name, u.username, u.phone_number, u.role, u.created_at, u.is_active,
                       a.street_name, a.street_number, a.apartment_suite, a.city, a.postal_code
                FROM users u
                LEFT JOIN addresses a ON u.address_id = a.id
                WHERE u.is_active = TRUE
            ';

            $params = [
                'limit' => $limit,
                'offset' => $offset
            ];

            if ($search) {
                $sql .= ' AND (u.email ILIKE :search OR u.username ILIKE :search OR u.first_name ILIKE :search OR u.last_name ILIKE :search)';
                $params['search'] = "%$search%";
            }

            $sql .= " ORDER BY $sortBy $sortDir LIMIT :limit OFFSET :offset";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getAllUsers: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_FETCH_USER);
        }
    }

    public function findUserById(int $id): ?array
    {
        try {
            $stmt = $this->connection->prepare('
                SELECT u.id, u.email, u.first_name, u.last_name, u.username, u.phone_number, u.role, u.created_at, u.is_active,
                       a.street_name, a.street_number, a.apartment_suite, a.city, a.postal_code
                FROM users u
                LEFT JOIN addresses a ON u.address_id = a.id
                WHERE u.id = :id
                LIMIT 1
            ');

            $stmt->execute(['id' => $id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user ?: null;
        } catch (PDOException $e) {
            error_log("Database error in findUserById: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_FETCH_USER);
        }
    }

    public function searchUsers(?string $search = null, string $sortBy = 'created_at', string $sortDir = 'DESC', string $status = 'all', int $limit = 50, int $offset = 0): array
    {
        try {
            $validSortBy = [
                'email' => 'u.email',
                'first_name' => 'u.first_name',
                'role' => 'u.role',
                'created_at' => 'u.created_at',
            ];
            $sortBy = $validSortBy[$sortBy] ?? 'u.created_at';
            $sortDir = strtoupper($sortDir) === 'ASC' ? 'ASC' : 'DESC';
            $validStatus = ['all', 'active', 'banned'];
            $status = in_array($status, $validStatus) ? $status : 'all';

            $sql = '
            SELECT u.id, u.email, u.first_name, u.last_name, u.username, u.phone_number, u.role, u.created_at, u.is_active,
                   a.street_name, a.street_number, a.apartment_suite, a.city, a.postal_code
            FROM users u
            LEFT JOIN addresses a ON u.address_id = a.id
        ';

            $params = [
                'limit' => $limit,
                'offset' => $offset
            ];

            $conditions = [];

            if ($status === 'active')
                $conditions[] = 'u.is_active = TRUE';
            elseif ($status === 'banned')
                $conditions[] = 'u.is_active = FALSE';

            if ($search) {
                $conditions[] = '(u.email ILIKE :search OR u.username ILIKE :search OR u.first_name ILIKE :search OR u.last_name ILIKE :search)';
                $params['search'] = "%$search%";
            }

            if (!empty($conditions))
                $sql .= ' WHERE ' . implode(' AND ', $conditions);

            $sql .= " ORDER BY $sortBy $sortDir LIMIT :limit OFFSET :offset";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in searchUsers: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_FETCH_USER);
        }
    }

    public function updateUserRole(int $id, string $newRole): void
    {
        try {
            $stmt = $this->connection->prepare('
                UPDATE users
                SET role = :role
                WHERE id = :id
            ');

            $stmt->execute([
                'role' => $newRole,
                'id' => $id
            ]);

            if ($stmt->rowCount() === 0)
                throw new UserRepoExc(UserRepoExc::USER_NOT_FOUND);
        } catch (PDOException $e) {
            error_log("Database error in updateUserRole: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_UPDATE_USER);
        }
    }

    public function editUser(int $id, string $firstName, string $lastName, string $username, string $email, ?string $phoneNumber, string $role, string $streetName, string $streetNumber, string $city, string $postalCode, ?string $apartmentSuite = null): void
    {
        try {
            $this->connection->beginTransaction();

            $validatedRole = UserRole::from($role)->value;

            $addressIdStmt = $this->connection->prepare('SELECT address_id FROM users WHERE id = :id LIMIT 1');
            $addressIdStmt->execute(['id' => $id]);
            $addressId = $addressIdStmt->fetchColumn();
            if ($addressId === false) {
                $this->connection->rollBack();
                throw new UserRepoExc(UserRepoExc::USER_NOT_FOUND);
            }

            $stmt = $this->connection->prepare('
                UPDATE users
                SET first_name = :first_name,
                    last_name = :last_name,
                    username = :username,
                    email = :email,
                    phone_number = :phone_number,
                    role = :role
                WHERE id = :id
            ');

            $stmt->execute([
                'id' => $id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'username' => $username,
                'email' => $email,
                'phone_number' => $phoneNumber,
                'role' => $validatedRole
            ]);

            if ($addressId !== null) {
                $addressStmt = $this->connection->prepare('
                    UPDATE addresses
                    SET street_name = :street_name,
                        street_number = :street_number,
                        apartment_suite = :apartment_suite,
                        city = :city,
                        postal_code = :postal_code
                    WHERE id = :address_id
                ');

                $addressStmt->execute([
                    'address_id' => (int)$addressId,
                    'street_name' => $streetName,
                    'street_number' => $streetNumber,
                    'apartment_suite' => $apartmentSuite,
                    'city' => $city,
                    'postal_code' => $postalCode,
                ]);
            } else {
                $newAddressStmt = $this->connection->prepare('
                    INSERT INTO addresses (street_name, street_number, apartment_suite, city, postal_code)
                    VALUES (:street_name, :street_number, :apartment_suite, :city, :postal_code)
                    RETURNING id
                ');

                $newAddressStmt->execute([
                    'street_name' => $streetName,
                    'street_number' => $streetNumber,
                    'apartment_suite' => $apartmentSuite,
                    'city' => $city,
                    'postal_code' => $postalCode,
                ]);

                $newAddressId = $newAddressStmt->fetchColumn();
                if ($newAddressId === false) {
                    $this->connection->rollBack();
                    throw new UserRepoExc(UserRepoExc::FAILED_TO_UPDATE_USER);
                }

                $linkStmt = $this->connection->prepare('UPDATE users SET address_id = :address_id WHERE id = :id');
                $linkStmt->execute([
                    'address_id' => (int)$newAddressId,
                    'id' => $id,
                ]);
            }

            $this->connection->commit();
        } catch (PDOException $e) {
            if ($this->connection->inTransaction())
                $this->connection->rollBack();

            error_log("Database error in editUser: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_UPDATE_USER);
        }
    }

    public function deactivateUser(int $id): void
    {
        try {
            $stmt = $this->connection->prepare('
                UPDATE users
                SET is_active = FALSE
                WHERE id = :id
            ');

            $stmt->execute(['id' => $id]);

            if ($stmt->rowCount() === 0)
                throw new UserRepoExc(UserRepoExc::USER_NOT_FOUND);
        } catch (PDOException $e) {
            error_log("Database error in deactivateUser: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_UPDATE_USER);
        }
    }

    public function reactivateUser(int $id): void
    {
        try {
            $stmt = $this->connection->prepare('
                UPDATE users
                SET is_active = TRUE
                WHERE id = :id
            ');

            $stmt->execute(['id' => $id]);

            if ($stmt->rowCount() === 0)
                throw new UserRepoExc(UserRepoExc::USER_NOT_FOUND);
        } catch (PDOException $e) {
            error_log("Database error in reactivateUser: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_UPDATE_USER);
        }
    }
    //endregion


    //region Statistics (Admin-only)
    public function getTotalRegisteredUserCount(): int
    {
        try {
            $stmt = $this->connection->prepare('
                SELECT COUNT(*) as count FROM users
            ');

            $stmt->execute();

            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database error in getTotalRegisteredUserCount: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_FETCH_USER);
        }
    }

    public function getUserCount(): int
    {
        try {
            $stmt = $this->connection->prepare('
                SELECT COUNT(*) as count FROM users 
                WHERE is_active = TRUE
            ');

            $stmt->execute();

            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database error in getUserCount: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_FETCH_USER);
        }
    }

    public function getActiveOrderCount(): int
    {
        try {
            $stmt = $this->connection->prepare('
                SELECT COUNT(*) as count FROM orders 
                WHERE order_status IN (:status1, :status2) 
                AND is_active = TRUE
            ');
            $stmt->execute([
                'status1' => 'Open',
                'status2' => 'Processing'
            ]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database error in getActiveOrderCount: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_FETCH_USER);
        }
    }

    public function getMonthlyRevenue(): float
    {
        try {
            $stmt = $this->connection->prepare('
                SELECT COALESCE(SUM(total_cost), 0) as revenue FROM orders 
                WHERE order_status = :status
                AND is_active = TRUE 
                AND DATE_TRUNC(:interval1, order_date) = DATE_TRUNC(:interval2, NOW())
            ');
            $stmt->execute([
                'status' => 'Completed',
                'interval1' => 'month',
                'interval2' => 'month'
            ]);
            return (float)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database error in getMonthlyRevenue: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_FETCH_USER);
        }
    }
    //endregion


    //region Homepage Content Management
    public function getPageContent(string $pageName): ?string
    {
        try {
            $stmt = $this->connection->prepare('
                SELECT cb.encoded_string 
                FROM content_blocks cb
                JOIN pages p ON cb.page_id = p.id
                WHERE p.page_name = :page_name
                LIMIT 1
            ');
            $stmt->execute(['page_name' => $pageName]);
            $result = $stmt->fetchColumn();
            return $result === false ? null : $result;
        } catch (PDOException $e) {
            error_log("Database error in getPageContent: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_FETCH_USER);
        }
    }

    public function updatePageContent(string $pageName, string $content): void
    {
        try {
            $this->connection->beginTransaction();

            // Get or create page
            $stmt = $this->connection->prepare('SELECT id FROM pages WHERE page_name = :page_name');
            $stmt->execute(['page_name' => $pageName]);
            $pageId = $stmt->fetchColumn();

            if ($pageId === false) {
                // Page doesn't exist, create it
                $stmt = $this->connection->prepare('INSERT INTO pages (page_name) VALUES (:page_name) RETURNING id');
                $stmt->execute(['page_name' => $pageName]);
                $pageId = $stmt->fetchColumn();
            }

            // Check if content block already exists for this page
            $stmt = $this->connection->prepare('SELECT id FROM content_blocks WHERE page_id = :page_id LIMIT 1');
            $stmt->execute(['page_id' => $pageId]);
            $contentId = $stmt->fetchColumn();

            if ($contentId === false) {
                // Insert new content block
                $stmt = $this->connection->prepare('
                    INSERT INTO content_blocks (page_id, encoded_string)
                    VALUES (:page_id, :content)
                ');
            } else {
                // Update existing content block
                $stmt = $this->connection->prepare('
                    UPDATE content_blocks 
                    SET encoded_string = :content
                    WHERE page_id = :page_id
                ');
            }

            $stmt->execute([
                'page_id' => $pageId,
                'content' => $content
            ]);

            $this->connection->commit();
        } catch (PDOException $e) {
            if ($this->connection->inTransaction())
                $this->connection->rollBack();
            error_log("Database error in updatePageContent: " . $e->getMessage());
            throw new UserRepoExc(UserRepoExc::FAILED_TO_UPDATE_USER);
        }
    }
    //endregion
}