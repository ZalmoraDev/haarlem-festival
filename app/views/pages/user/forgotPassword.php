<?php

use app\core\Csrf;
include_once __DIR__ . "/../../layouts/sidebar.php";
?>

<body class="fest-body">
<main class="fest-main">
    <section class="fest-container">
        <header class="fest-container-sm w-30 h-30 border-0">
            <img src="/assets/icons/logo/logo-000.svg" alt="<?= $_ENV['SITE_NAME'] ?> logo">
        </header>
        <header class="flex flex-col justify-center items-center gap-2">
            <h1 class="text-4xl">Forgot password</h1>
            <p class="text-neutral-400">Enter your email to receive a reset link.</p>
        </header>
        <section class="gap-4 flex flex-col w-full items-center">
            <form action="/auth/forgot-password" method="POST" class="flex flex-col justify-center items-center gap-2 w-full max-w-md">
                <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                <input type="email" class="fest-input" placeholder="Email" name="email" required
                       aria-label="Email address">
                <button type="submit" class="fest-btn w-full mt-4 cursor-pointer">Send reset link</button>
            </form>
            <p class="text-neutral-400"><a href="/login" class="text-black underline cursor-pointer">Back to login</a></p>
        </section>
    </section>
</main>
</body>
