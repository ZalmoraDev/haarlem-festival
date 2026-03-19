<?php

namespace app\core;

use app\views\components\ToastComp;

/** View renderer for passing view, title & parameters */
final readonly class View
{
    /** Handle rendering of views with provided title and parameters. */
    public static function render(string $view, string $title, int $pageID, array $controllerData = []): void
    {
        /** Global access to session/controller data from view files */
        $viewData = [
            'viewFile' => __DIR__ . '/../views/pages/' . $view,
            'viewTitle' => $title,

            // $_SESSION is set at login under name 'auth'
            // Address indexes are not array items like the other values,
            // since it uses the `address` model object, storing as members
            'user' => [
                'id' => $_SESSION['auth']['id'] ?? null,
                'firstName' => $_SESSION['auth']['firstName'] ?? null,
                'lastName' => $_SESSION['auth']['lastName'] ?? null,
                'username' => $_SESSION['auth']['username'] ?? null,
                'email' => $_SESSION['auth']['email'] ?? null,
                'phoneNumber' => $_SESSION['auth']['phoneNumber'] ?? null,

                'address' => $_SESSION['auth']['address'] ?? null,
                'streetName' => $_SESSION['auth']['address']->streetAddress ?? null,
                'streetNumber' => $_SESSION['auth']['address']->streetNumber ?? null,
                'apartmentSuite' => $_SESSION['auth']['address']->apartmentSuite ?? null,
                'city' => $_SESSION['auth']['address']->city ?? null,
                'postalCode' => $_SESSION['auth']['address']->postalCode ?? null,

                'role' => $_SESSION['auth']['role'] ?? null,
            ],

            // Get successes/info/errors(exception) messages under the ['flash']['<HANDLE>'] array naming from controllers
            'flash' => [
                'successes' => $_SESSION['flash_successes'] ?? [],
                'info' => $_SESSION['flash_info'] ?? [],
                'errors' => $_SESSION['flash_errors'] ?? [],
            ],

            // Used for setting valid declaring valid inline JS scripts, preventing CSP blocking
            'csp_nonce' => $_SESSION['csp_nonce'] ?? '',
        ];

        // Merge controller data, and extract to variables for use in Views
        $viewData = array_merge($viewData, $controllerData);
        self::addToastNotifications($viewData);
        extract($viewData, EXTR_SKIP);

        // Unset all flash data, preventing showing in unrelated views
        unset(
            $_SESSION['flash_successes'],
            $_SESSION['flash_info'],
            $_SESSION['flash_errors']
        );
        require __DIR__ . '/../views/layouts/base.php';

        self::renderEditorComponents($pageID);
    }

    private static function renderEditorComponents(int $pageID)
    {
        //get page from database


        //get content blocks matching page from the database


        //render each content block


    }

    /** Retrieve site name from .env, preventing repeatedly hardcoding.
     * Optionally added to be used in View::render requests. */
    public static function addSiteName(): string
    {
        return " | " . $_ENV['SITE_NAME'];
    }

    /** Add toast notifications to the view ONLY if there are any flash messages. */
    private static function addToastNotifications($viewData): void
    {
        // Render toast component if there are flash messages to show
        if ($viewData['flash']['successes'] || $viewData['flash']['info'] || !empty($viewData['flash']['errors']))
            ToastComp::render($viewData['flash'], $viewData['csp_nonce']);
    }
}