<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use app\controllers\AdminCtrl;
use app\controllers\UserCtrl;
use app\controllers\PaymentCtrl;
use app\controllers\DanceCtrl;
use app\controllers\HistoryCtrl;
use app\controllers\JazzCtrl;
use app\controllers\YummyCtrl;
use app\core\Csp;
use app\routing\Routes;
use app\routing\Router;

use app\models\enums\UserRole;

use app\services\AdminServ;
use app\services\PaymentServ;

use app\services\EmailServ;
use app\services\PasswordResetServ;
use app\services\UserServ;
use app\services\ReCaptchaServ;
use app\services\DanceServ;
use app\services\HistoryServ;
use app\services\JazzServ;
use app\services\YummyServ;

use app\repositories\UserRepo;
use app\repositories\DanceRepo;
use app\repositories\HistoryRepo;
use app\repositories\JazzRepo;
use app\repositories\YummyRepo;
use app\repositories\PaymentRepo;

// -------------------- Error reporting --------------------
// TODO: REMOVE OR DISABLE IN PRODUCTION

// (Uncomment for debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// -------------------- Session & .env config --------------------
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // https://www.php.net/manual/en/session.configuration.php
    session_start([
        'use_strict_mode' => true, // prevent CSRF & uninitialized session IDs
        'cookie_httponly' => true, // prevent JS access to cookies (XSS)
        'cookie_samesite' => 'Strict', // prevent cross-site usage
    ]);
}

// Set default role only if not already set
if (!isset($_SESSION['auth']['role']))
    $_SESSION['auth']['role'] = UserRole::Visitor; // Default role for unauthenticated users

/** vlucas/phpdotenv, set up environment variables, autoload /.env file
 * @see https://packagist.org/packages/vlucas/phpdotenv
 */
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$dotenv->required([
    'SITE_NAME', 'SITE_URL',
    'DB_TYPE', 'DB_HOST', 'DB_PORT', 'DB_DATABASE',
    'DB_USERNAME', 'DB_PASSWORD']);

// -------------------- Security Headers --------------------
// See HTTP headers:
// 1) https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference
// 2) https://developer.mozilla.org/en-US/docs/Glossary/Fetch_directive
// 3) https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Security-Policy/script-src#unsafe_inline_script

header("Access-Control-Allow-Methods: GET, POST"); // Only allow GET and POST requests
header("Access-Control-Allow-Origin: " . $_ENV['SITE_URL']); // Only allow requests from this host's URL
header("Content-Security-Policy: " .
    "default-src 'self'; " .
    "script-src 'self' 'nonce-" . Csp::getNonce() . "' https://www.google.com https://www.gstatic.com; " .
    "frame-src https://www.google.com; " .
    "style-src 'self' 'unsafe-inline';"); // CSP to mitigate XSS attacks, allow Google ReCaptcha
header("X-Content-Type-Options: nosniff"); // Prevent MIME type sniffing
header("X-Frame-Options: SAMEORIGIN"); // Prevent clickjacking
header("Referrer-Policy: strict-origin-when-cross-origin"); // Control referrer information

// -------------------- DI Container setup --------------------
// Repositories
$userRepo = new UserRepo();
$danceRepo = new DanceRepo();
$historyRepo = new HistoryRepo();
$jazzRepo = new JazzRepo();
$yummyRepo = new YummyRepo();
$paymentRepo = new PaymentRepo();


// Services
$recaptchaServ = new ReCaptchaServ();
$emailServ = new EmailServ();
$passwordResetServ = new PasswordResetServ($userRepo, $emailServ);
$userServ = new UserServ($userRepo, $recaptchaServ, $emailServ, $passwordResetServ);
$paymentServ = new PaymentServ($paymentRepo);

$danceServ = new DanceServ($danceRepo);
$historyServ = new HistoryServ($historyRepo);
$jazzServ = new JazzServ($jazzRepo);
$yummyServ = new YummyServ($yummyRepo);

// ControllersWeb
$adminCtrl = new AdminCtrl($userServ);
$userCtrl = new UserCtrl($userServ);
$paymentCtrl = new PaymentCtrl($paymentServ);


$danceCtrl = new DanceCtrl($danceServ);
$historyCtrl = new HistoryCtrl($historyServ);
$jazzCtrl = new JazzCtrl($jazzServ);
$yummyCtrl = new YummyCtrl($yummyServ);

// -------------------- Routing setup & Router dispatch --------------------
// Controller map for Routes.php
$controllers = [
    'admin' => $adminCtrl,
    'user' => $userCtrl,
    'dance' => $danceCtrl,
    'history' => $historyCtrl,
    'jazz' => $jazzCtrl,
    'yummy' => $yummyCtrl,
    'payment' => $paymentCtrl
];

$routes = new Routes($controllers);
$router = new Router($routes->dispatcher(), $userServ);
$router->dispatch();