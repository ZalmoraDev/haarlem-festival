<?php

namespace app\controllers;

use app\core\View;
use app\models\enums\PageIndex;
use app\services\interfaces\IUserServ;
use app\services\exceptions\BaseServExc;
use Exception;

/** Controller for user-related actions
 * - GET: Serve landing, login/signup & settings pages
 * - POST: Handle user account creation, editing & deletions */
final readonly class UserCtrl extends BaseCtrl
{
    private IUserServ $userServ;

    public function __construct(IUserServ $userServ)
    {
        $this->userServ = $userServ;
    }

    //region GET Requests

    /** GET /, Landingpage for visitors */
    public function landingPage(): void
    {
        View::render('/landingPage.php', $_ENV['SITE_NAME'], PageIndex::LandingPage->value);
    }

    /** GET /settings, User account settings page */
    public function settingsPage(): void
    {
        View::render('/user/settings.php', "Settings" . View::addSiteName(), PageIndex::SettingsPage->value);
    }

    /** GET /login, serves login page */
    public function loginPage(): void
    {
        View::render('/user/login.php', "Login" . View::addSiteName(), PageIndex::LoginPage->value);
    }

    /** GET /forgot-password, serves forgot password page */
    public function forgotPasswordPage(): void
    {
        View::render('/user/forgotPassword.php', "Forgot Password" . View::addSiteName(), PageIndex::ForgotPasswordPage->value);
    }

    /** GET /reset-password, serves reset password page */
    public function resetPasswordPage(): void
    {
        $token = $_GET['token'] ?? '';

        View::render('/user/resetPassword.php', "Reset Password" . View::addSiteName(), PageIndex::ResetPasswordPage->value, [
            'token' => $token
        ]);
    }

    /** GET /signup, serves signup page */
    public function signupPage(): void
    {
        View::render('/user/signup.php', "Signup" . View::addSiteName(), PageIndex::SignupPage->value);
    }

    /** GET /attribution, serves attribution page */
    public function attributionPage(): void
    {
        View::render('/attribution.php', "Attribution" . View::addSiteName(), PageIndex::AttributionPage->value);
    }
    //endregion


    //region POST Requests
    //region Auth Session
    /** POST /auth/login, processes login form submission */
    public function login(): void
    {
        try {
            $this->userServ->login(
                $_POST['email'] ?? '',
                $_POST['password'] ?? ''
            );
            $_SESSION['flash_successes'][] = "You are now logged in.";

            // Redirect admin users to dashboard, regular users to home
            $redirect = $_SESSION['auth']['role']->value === 'Admin' ? '/admin/dashboard' : '/';
        } catch (Exception $e) {
            $this->handleException($e);
            $redirect = '/login';
        }
        $this->redirect($redirect);
    }

    /** POST /auth/signup, processes signup form submission */
    public function signup(): void
    {
        try {
            $this->userServ->signup(
                $_POST['first_name'] ?? '',
                $_POST['last_name'] ?? '',
                $_POST['username'] ?? '',
                $_POST['email'] ?? '',
                $_POST['phone_number'] ?? '',
                $_POST['password'] ?? '',
                $_POST['password_confirm'] ?? '',
                $_POST['street_name'] ?? '',
                $_POST['street_number'] ?? '',
                $_POST['apartment_suite'] ?? '',
                $_POST['city'] ?? '',
                $_POST['postal_code'] ?? '',
                $_POST['g-recaptcha-response'] ?? null
            );
            $redirect = '/';
        } catch (Exception $e) {
            $this->handleException($e);
            $redirect = '/signup';
        }
        $this->redirect($redirect);
    }

    /** POST /auth/logout, serves logout action */
    public function logout(): void
    {
        $this->userServ->logout();
        $_SESSION['flash_info'][] = "You have been logged out.";
        $this->redirect('/');
    }

    /** POST /auth/forgot-password, starts password reset flow */
    public function requestPasswordReset(): void
    {
        try {
            $this->userServ->requestPasswordReset($_POST['email'] ?? '');
            $_SESSION['flash_successes'][] = "If an account exists for this email, a reset link has been sent.";
            $redirect = '/login';
        } catch (BaseServExc $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
            $redirect = '/forgot-password';
        } catch (Exception) {
            $_SESSION['flash_errors'][] = "An unexpected error occurred.";
            $redirect = '/forgot-password';
        }

        header("Location: $redirect", true, 302);
        exit;
    }

    /** POST /auth/reset-password, completes password reset flow */
    public function resetPassword(): void
    {
        try {
            $this->userServ->resetPassword(
                $_POST['token'] ?? '',
                $_POST['password'] ?? '',
                $_POST['password_confirm'] ?? ''
            );
            $_SESSION['flash_successes'][] = "Your password has been reset. You can now log in.";
            $redirect = '/login';
        } catch (BaseServExc $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
            $redirect = '/reset-password?token=' . urlencode($_POST['token'] ?? '');
        } catch (Exception) {
            $_SESSION['flash_errors'][] = "An unexpected error occurred.";
            $redirect = '/reset-password?token=' . urlencode($_POST['token'] ?? '');
        }

        header("Location: $redirect", true, 302);
        exit;
    }
    //endregion Auth Session


    //region Account Management
    /** POST /settings/edit, Handle user account edit form submission */
    public function handleEdit(): void
    {
        try {
            $this->userServ->editAccount(
                $_POST['first_name'] ?? '',
                $_POST['last_name'] ?? '',
                $_POST['username'] ?? '',
                $_POST['email'] ?? '',
                $_POST['phone_number'] ?? '',
                $_POST['password'] ?? '',
                $_POST['password_confirm'] ?? '',
                $_POST['street_name'] ?? '',
                $_POST['street_number'] ?? '',
                $_POST['apartment_suite'] ?? '',
                $_POST['city'] ?? '',
                $_POST['postal_code'] ?? ''
            );
            $_SESSION['flash_successes'][] = "Account updated successfully.";
        } catch (Exception $e) {
            $this->handleException($e);
        }
        $this->redirect('/settings');
    }

    /** POST /settings/delete, Handle user account deletion form submission */
    public function handleDeletion(): void
    {
        try {
            $this->userServ->deleteAccount(
                $_POST['confirm_username'] ?? ''
            );
            $_SESSION['flash_successes'][] = "Account deleted successfully.";
            $redirect = "/";
        } catch (Exception $e) {
            $this->handleException($e);
            $redirect = "/settings";
        }
        $this->redirect($redirect);
    }
    //endregion Account Management
    //endregion POST Requests
}