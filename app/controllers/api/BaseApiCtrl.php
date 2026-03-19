<?php

namespace app\controllers\api;

use app\core\Csrf;
use app\models\enums\UserRole;
use app\services\interfaces\IUserServ;
use app\services\exceptions\UserServExc;

/**
 * Base class for API controllers handling Router-style authentication and authorization.
 * Ensures that access to endpoints are validated, preventing deleted/removed users
 * from performing actions via AJAX without page refresh, which Router cannot handle.
 */
abstract class BaseApiCtrl
{
    protected IUserServ $IUserServ;

    public function __construct(IUserServ $IUserServ)
    {
        $this->IUserServ = $IUserServ;
    }

    //region Auth
    /** Validates the request for authentication and project access
     * @throws UserServExc if authentication or authorization fails
     */
    protected function authenticateRequest(UserRole $requiredRole): void
    {
        header('Content-Type: application/json');

        // Verify CSRF token (Router does not handle this for API requests)
        Csrf::requireVerification($_POST['csrf'] ?? null);

        // Get current user ID from session
        $userId = $_SESSION['auth']['id'] ?? null;
        if (!$userId) {
            $this->jsonError(401, 'Not authenticated');
            exit;
        }

        // Check if user has required access to the project
        $this->IUserServ->validateAccessRole($requiredRole);
    }
    //endregion


    //region Helper Methods

    /** Returns a JSON error response with appropriate HTTP status code
     */
    protected function jsonError(int $statusCode, string $errorMessage): void
    {
        http_response_code($statusCode);
        echo json_encode(['success' => false, 'error' => $errorMessage]);
    }

    /** Returns a JSON success response with appropriate HTTP status code
     */
    protected function jsonSuccess(int $statusCode, array $data): void
    {
        http_response_code($statusCode);
        echo json_encode(array_merge(['success' => true], $data));
    }
    //endregion
}
