<?php

namespace app\routing;

use app\core\Csrf;

use app\controllers\ErrorCtrl;
use app\services\UserServ;

use app\services\exceptions\UserServExc;
use app\services\exceptions\BaseServExc;
use app\services\exceptions\PaymentServExc;
use Exception;

use FastRoute;

final class Router
{
    // Dependency Injection of FastRoute dispatcher routes, created in Routes.php
    private FastRoute\Dispatcher $dispatcher;
    private UserServ $userServ;

    public function __construct(FastRoute\Dispatcher $dispatcher, UserServ $authServ)
    {
        $this->dispatcher = $dispatcher;
        $this->userServ = $authServ;
    }

    /** nikic/fast-route | https://packagist.org/packages/nikic/fast-route.
     * Modification on the basic usage implementation from docs, contains additional header data regarding route access rights. */
    public function dispatch(): void
    {
        // Fetch method and URI from somewhere
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?'))
            $uri = substr($uri, 0, $pos);
        $uri = rawurldecode($uri);

        // Use dispatcher retrieved from Routes.php
        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);

        $errorController = new ErrorCtrl();
        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                $errorController->notFound();
                exit;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $errorController->methodNotAllowed();
                exit;
            case FastRoute\Dispatcher::FOUND:
                try {
                    $handler = $routeInfo[1];
                    $pathParams = $routeInfo[2];
                    $routeReqRole = $handler['accessRole'];
                    $isStripeWebhook = ($uri === '/api/webhook/stripe');
                    if (!$isStripeWebhook) {

                    if ($_SERVER['REQUEST_METHOD'] === 'POST') Csrf::requireVerification($_POST['csrf'] ?? null);
                    
                    $this->userServ->redirectWhenLoggedIn($handler['action'][1]);
                    $this->userServ->validateAccessRole($routeReqRole);
                    }

                    call_user_func_array($handler['action'], $pathParams);
                    if ($isStripeWebhook) exit;
                    break;
                } catch (UserServExc $e) {
                    // Thrown by authServ::redirectWhenLoggedIn()
                   if ($e->getMessage() === UserServExc::ALREADY_LOGGED_IN)
                        $_SESSION['flash_info'][] = $e->getMessage();
                    else
                        $_SESSION['flash_errors'][] = $e->getMessage();
                } catch (PaymentServExc $e) {
                    if ($isStripeWebhook ?? false) {
                        http_response_code($e->getCode() ?: 500);
                        exit($e->getMessage());}
                        else {$_SESSION['flash_errors'][] = $e->getMessage();
                    }
                } catch (\Exception $e) {
                      if ($isStripeWebhook ?? false) {
                        http_response_code($e->getCode() ?: 500);
                        exit($e->getMessage("An unexpected error occurred"));
                    }
                    else $_SESSION['flash_errors'][] = "An unexpected error occurred.";
                }
                // Redirect to previous page or fallback to home
                header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'), true, 302);
                exit;
        
        }
    }
}