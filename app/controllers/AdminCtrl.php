<?php

namespace app\controllers;

use app\core\View;
use app\models\enums\PageIndex;
use app\services\exceptions\ValidationServExc;
use app\services\interfaces\IUserServ;
use Exception;

/** Controller for admin panel actions
 * - GET: Display admin dashboard and management pages
 * - POST: Handle admin actions */
final readonly class AdminCtrl extends BaseCtrl
{
    private IUserServ $userServ;

    public function __construct(IUserServ $userServ)
    {
        $this->userServ = $userServ;
    }


    //region GET Requests

    /** GET /admin/dashboard, Admin control panel */
    public function dashboardPage(): void
    {
        try {
            $stats = [
                'totalRegisteredUsers' => $this->userServ->getTotalRegisteredUserCount(),
                'activeUsers' => $this->userServ->getTotalUserCount(),
                'activeOrders' => $this->userServ->getActiveOrderCount(),
                'monthlyRevenue' => $this->userServ->getMonthlyRevenue(),
            ];
        } catch (Exception $e) {
            $this->handleException($e);
            $stats = [
                'totalRegisteredUsers' => 0,
                'activeUsers' => 0,
                'activeOrders' => 0,
                'monthlyRevenue' => 0,
            ];
        }

        View::render('/admin/dashboard.php', "Admin Dashboard" . View::addSiteName(), PageIndex::AdminDashboard->value, $stats);
    }

    /** GET /admin/users, User management page */
    public function userManagementPage(): void
    {
        $search = $_GET['search'] ?? null;
        $sortBy = $_GET['sort_by'] ?? 'created_at';
        if ($sortBy === 'firstname')
            $sortBy = 'first_name';
        $sortDir = $_GET['sort_dir'] ?? 'DESC';
        $status = $_GET['status'] ?? 'all';

        try {
            $users = $this->userServ->searchUsers($search, $sortBy, $sortDir, $status);
        } catch (Exception $e) {
            $this->handleException($e);
            $users = [];
        }

        $pageData = [
            'users' => $users,
            'search' => $search,
            'sortBy' => $sortBy,
            'sortDir' => $sortDir,
            'status' => $status,
        ];

        View::render('/admin/userManagement.php', "User Management" . View::addSiteName(), PageIndex::AdminDashboard->value, $pageData);
    }

    /** GET /admin/users/{id}/edit, Edit user page */
    public function editUserPage(int $id): void
    {
        try {
            $user = $this->userServ->getUserById($id);
            View::render('/admin/editUser.php', "Edit User" . View::addSiteName(), PageIndex::AdminDashboard->value, $user);
        } catch (Exception $e) {
            $this->handleException($e);
            $this->redirect('/admin/users');
        }
    }

    /** GET /admin/homepage, Edit homepage content page */
    public function homepageEditPage(): void
    {
        try {
            $content = $this->userServ->getPageContent('homepage');
        } catch (Exception $e) {
            $this->handleException($e);
            $content = '';
        }

        $pageData = [
            'content' => $content,
        ];

        View::render('/admin/homepageEdit.php', "Edit Homepage" . View::addSiteName(), PageIndex::AdminDashboard->value, $pageData);
    }
    //endregion GET Requests


    //region POST Requests

    /** POST /admin/users/deactivate, Deactivate a user account */
    public function deactivateUser(): void
    {
        try {
            $id = (int) ($_POST['user_id'] ?? 0);
            $this->userServ->deactivateUser($id);
            $_SESSION['flash_successes'][] = 'User deactivated successfully';
        } catch (Exception $e) {
            $this->handleException($e);
        }
        $this->redirect('/admin/users');
    }


    /** POST /admin/users/reactivate, Reactivate a user account */
    public function reactivateUser(): void
    {
        try {
            $id = (int) ($_POST['user_id'] ?? 0);
            $this->userServ->reactivateUser($id);
            $_SESSION['flash_successes'][] = 'User reactivated successfully';
        } catch (Exception $e) {
            $this->handleException($e);
        }
        $this->redirect('/admin/users');
    }

    /** POST /admin/users/{id}/edit, Handle user edit form submission */
    public function handleEditUser(int $id): void
    {
        try {
            $this->userServ->editUser(
                $id,
                $_POST['first_name'] ?? '',
                $_POST['last_name'] ?? '',
                $_POST['username'] ?? '',
                $_POST['email'] ?? '',
                $_POST['role'] ?? 'Customer',
                $_POST['street_name'] ?? '',
                (int)($_POST['street_number'] ?? 0),
                $_POST['city'] ?? '',
                $_POST['postal_code'] ?? '',
                $_POST['phone_number'] ?? null,
                $_POST['apartment_suite'] ?? null
            );
            $_SESSION['flash_successes'][] = 'User updated successfully';
            $this->redirect('/admin/users');
        } catch (ValidationServExc $e) {
            // Validation error - stay on edit page and show error message
            $_SESSION['flash_errors'][] = $e->getMessage();
            // Re-render the edit page with the user data
            try {
                $user = $this->userServ->getUserById($id);
                View::render('/admin/editUser.php', "Edit User" . View::addSiteName(), PageIndex::AdminDashboard->value, $user);
            } catch (Exception $innerE) {
                $this->handleException($innerE);
                $this->redirect('/admin/users');
            }
        } catch (Exception $e) {
            $this->handleException($e);
            $this->redirect('/admin/users');
        }
    }

    /** POST /admin/homepage, Update homepage content */
    public function homepageEdit(): void
    {
        try {
            $content = $_POST['content'] ?? '';
            $this->userServ->updatePageContent('homepage', $content);
            $_SESSION['flash_successes'][] = 'Homepage content updated successfully';
        } catch (Exception $e) {
            $this->handleException($e);
        }
        $this->redirect('/admin/homepage');
    }

    //endregion POST Requests
}
