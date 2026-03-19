<?php

use app\core\Csrf;
use app\core\Escaper;
include_once __DIR__ . "/../../layouts/sidebar.php";

/** @var array $viewData */
$token = $viewData['token'] ?? '';
?>

<body class="fest-body">
<main class="fest-main">
    <section class="fest-container">
        <header class="fest-container-sm w-30 h-30 border-0">
            <img src="/assets/icons/logo/logo-000.svg" alt="<?= $_ENV['SITE_NAME'] ?> logo">
        </header>
        <header class="flex flex-col justify-center items-center gap-2">
            <h1 class="text-4xl">Reset password</h1>
            <p class="text-neutral-400">Choose a new password for your account.</p>
        </header>
        <section class="gap-4 flex flex-col w-full items-center">
            <form action="/auth/reset-password" method="POST" class="flex flex-col justify-center items-center gap-2 w-full max-w-md">
                <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                <input type="hidden" name="token" value="<?= Escaper::html($token) ?>">

                <input type="password" class="fest-input" placeholder="New password" name="password" required
                       minlength="12"
                       title="Must have: 1 lowercase, 1 uppercase, 1 digit, no spaces, 12-64 characters"
                       pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])\S{12,64}$"
                       aria-label="Required new password">
                <input type="password" class="fest-input" placeholder="Confirm new password" name="password_confirm" required
                       minlength="12" aria-label="Required confirm password">
                <button type="submit" class="fest-btn w-full mt-4 cursor-pointer">Reset password</button>
            </form>
            <p class="text-neutral-400"><a href="/login" class="text-black underline cursor-pointer">Back to login</a></p>
        </section>
    </section>
</main>
</body>
