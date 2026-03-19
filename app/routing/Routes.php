<?php

namespace app\routing;

use app\models\enums\AccessRole;
use FastRoute;

/** Separation of routes from router dispatching logic.
 * Defines all routes with their handlers and required roles to be evaluated in router. */
final class Routes
{
    private array $controllers;

    public function __construct(array $controllers)
    {
        $this->controllers = $controllers;
    }

    /** nikic/fast-route | https://packagist.org/packages/nikic/fast-route.
     * Route definitions with additional access rights logic, to be evaluated by router.
     * Uses route aliases instead of full $r->addRoute(METHOD, ...)*/
    public function dispatcher(): FastRoute\Dispatcher
    {
        return FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
            // Retrieve controllers and use them as conciser abbreviations in route handler definitions
            // Array mapping values retrieved from index.php when initializing Routes class
            $admin = $this->controllers['admin'];
            $user = $this->controllers['user'];
            
            $payment = $this->controllers['payment'];

            $dance = $this->controllers['dance'];
            $history = $this->controllers['history'];
            $jazz = $this->controllers['jazz'];
            $yummy = $this->controllers['yummy'];

            //region Shared Web Routes
            // AuthCtrl routes
            $r->get('/login', $this->route([$user, 'loginPage'], AccessRole::Visitor));
            $r->get('/signup', $this->route([$user, 'signupPage'], AccessRole::Visitor));
            $r->get('/forgot-password', $this->route([$user, 'forgotPasswordPage'], AccessRole::Visitor));
            $r->get('/reset-password', $this->route([$user, 'resetPasswordPage'], AccessRole::Visitor));

            $r->post('/auth/login', $this->route([$user, 'login'], AccessRole::Visitor));
            $r->post('/auth/signup', $this->route([$user, 'signup'], AccessRole::Visitor));
            $r->post('/auth/logout', $this->route([$user, 'logout'], AccessRole::Visitor));
            $r->post('/auth/forgot-password', $this->route([$user, 'requestPasswordReset'], AccessRole::Visitor));
            $r->post('/auth/reset-password', $this->route([$user, 'resetPassword'], AccessRole::Visitor));

            // UserCtrl routes
            $r->get('/', $this->route([$user, 'landingPage'], AccessRole::Visitor));
            $r->get('/settings', $this->route([$user, 'settingsPage'], AccessRole::Customer));
            $r->post('/user/edit', $this->route([$user, 'handleEdit'], AccessRole::Customer));
            $r->post('/user/delete', $this->route([$user, 'handleDeletion'], AccessRole::Customer));

            // Other routes
            $r->get('/attribution', $this->route([$user, 'attributionPage'], AccessRole::Visitor));
            //endregion Shared Routes


            
            // ---------------------------------------------------------------------------------------------------------
            
            //region Theme Web Routes
            //region Admin Routes
            $r->get('/admin/dashboard', $this->route([$admin, 'dashboardPage'], AccessRole::Admin));
            $r->get('/admin/users', $this->route([$admin, 'userManagementPage'], AccessRole::Admin));
            $r->get('/admin/users/{id:\d+}/edit', $this->route([$admin, 'editUserPage'], AccessRole::Admin));
            $r->get('/admin/homepage', $this->route([$admin, 'homepageEditPage'], AccessRole::Admin));
            
            $r->post('/admin/users/deactivate', $this->route([$admin, 'deactivateUser'], AccessRole::Admin));
            $r->post('/admin/users/reactivate', $this->route([$admin, 'reactivateUser'], AccessRole::Admin));
            $r->post('/admin/users/{id:\d+}/edit', $this->route([$admin, 'handleEditUser'], AccessRole::Admin));
            $r->post('/admin/homepage', $this->route([$admin, 'homepageEdit'], AccessRole::Admin));
            //endregion Admin Routes
            
            // PaymentCtrl routes
            $r->get('/payment', $this->route([$payment, 'homePage'], AccessRole::Visitor));
            $r->get('/payment/checkout', $this->route([$payment, 'checkoutPage'], AccessRole::Visitor));
            $r->post('/payment/checkout', $this->route([$payment, 'checkout'], AccessRole::Visitor));
            //endregion Payment Routes

            // DanceCtrl routes
            $r->get('/dance', $this->route([$dance, 'homePage'], AccessRole::Visitor));

            // HistoryCtrl routes
            $r->get('/history', $this->route([$history, 'homePage'], AccessRole::Visitor));

            // JazzCtrl routes
            $r->get('/jazz', $this->route([$jazz, 'homePage'], AccessRole::Visitor));
            // Jazz event detail (e.g. /jazz/42)
            $r->get('/jazz/{id:\d+}', $this->route([$jazz, 'detailPage'], AccessRole::Visitor));

            // YummyCtrl routes
            $r->get('/yummy', $this->route([$yummy, 'homePage'], AccessRole::Visitor));
            //endregion Theme Web Routes

            // ---------------------------------------------------------------------------------------------------------

            //region Shared API routes
            $r->post('/api/webhook/stripe', $this->route([$payment, 'handleWebhook'], AccessRole::Visitor));
            
            //endregion API Routes


            //region Theme API Routes

            // TODO: Add Theme API routes when eventually needed (routes must start with /api/)

            //endregion Theme API Routes
        });
    }

    /** Helper-object to create conciser route auth guard objects
     * @param array $action [controllerClass OBJ, 'classMethod' STRING]
     * @param AccessRole $reqRole Minimum role required to access route
     * @return array ['action' => $action, 'userRole' => $reqRole]
     */
    private function route(array $action, AccessRole $reqRole): array
    {
        return [
            'action' => $action,
            'accessRole' => $reqRole,
        ];
    }
}