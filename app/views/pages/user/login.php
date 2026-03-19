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
            <h1 class="text-4xl">Login</h1>
            <p class="text-neutral-400">Welcome back!</p>
        </header>
        <section class="gap-4 flex flex-col w-full items-center">
            <form action="/auth/login" method="POST" class="flex flex-col justify-center items-center gap-2">
                <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                <input type="email" class="fest-input" placeholder="Email" name="email" required
                       aria-label="Email address">
                <input type="password" class="fest-input" placeholder="Password" name="password" required
                       aria-label="Password">
                <button type="submit" class="fest-btn w-full mt-4 cursor-pointer">Login</button>
            </form>
                 <p><a href="/forgot-password" class="text-black underline cursor-pointer">Forgot password?</a></p>
            <p class="text-neutral-400">Don't have an account?
                <a href="/signup" class="text-black underline cursor-pointer">Sign up</a></p>
        </section>
    </section>
</main>
</body>