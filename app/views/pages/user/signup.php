<?php

use app\core\Csrf;
use app\core\Escaper;
include_once __DIR__ . "/../../layouts/sidebar.php";

/** @var array $viewData /app/Core/View.php View::render */
$countries = $viewData['countries'] ?? [];
?>

<!-- Google ReCaptcha -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<body class="fest-body">
<main class="fest-main">
    <section class="fest-container">
        <header class="w-30 h-30">
            <img src="/assets/icons/logo/logo-000.svg" alt="<?= $_ENV['SITE_NAME'] ?> logo">
        </header>
        <header class="flex flex-col justify-center items-center gap-2">
            <h1 class="text-4xl">Sign up</h1>
            <p class="text-neutral-400 items-center">Please enjoy your stay!</p>
        </header>

        <section class="gap-4 flex flex-col w-full items-center">
            <form action="/auth/signup" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">

                <!--region Identity and password -->
                <div class="flex flex-col gap-4 w-full">

                    <!-- Name -->
                    <div class="flex flex-col gap-2">
                        <h3 class="text-lg font-semibold text-neutral-400">Name</h3>
                        <input type="text" class="fest-input" placeholder="First name" name="first_name" required
                               aria-label="Required first name text input">
                        <input type="text" class="fest-input" placeholder="Last name" name="last_name" required
                               aria-label="Required last name text input">
                        <input type="text" class="fest-input" placeholder="Username [3-32]" name="username" required
                               aria-label="Required username text input, 3 to 32 characters">
                    </div>

                    <!-- Contact -->
                    <div class="flex flex-col gap-2">
                        <h3 class="text-lg font-semibold text-neutral-400">Contact</h3>
                        <input type="email" class="fest-input" placeholder="Email" name="email" required
                               aria-label="Required Email address text input, must be a valid email format">
                        <!-- TODO (STEF): Further verify if format is correct, also maybe add dependency for handling phonenumber input, here and backend-->
                        <input type="tel" class="fest-input" placeholder="Phone-number (optional)"
                               name="phone_number"
                               pattern="^\+\d{1,3}[\s\-]?\d{8,15}$"
                               title="Must start with + followed by country code (1-3 digits) and phone number (8-15 digits)"
                               aria-label="Optional phone-number text input, must be a valid international phone number format starting with +"
                               autocomplete="tel">
                    </div>

                    <!-- Password -->
                    <div class="flex flex-col gap-2">
                        <h3 class="text-lg font-semibold text-neutral-400">Password</h3>
                        <input type="password" class="fest-input" placeholder="Password" name="password" required
                               minlength="12"
                               title="Must have: 1 lowercase, 1 uppercase, 1 digit, no spaces, 12-64 characters"
                               pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])\S{12,64}$"
                               aria-label="Required password text input. Must have 1 lowercase, 1 uppercase, 1 digit, no spaces, 12-64 characters">
                        <input type="password" class="fest-input" placeholder="Confirm Password"
                               name="password_confirm"
                               required minlength="12" aria-label="Required confirm password text input">
                    </div>
                    <p class="text-xs text-neutral-400 w-full text-left mt-1">
                        Password must have:<br>1 lowercase, 1 uppercase, 1 digit,<br> no spaces, 12-64 chars
                    </p>
                </div>
                <!--endregion -->
                <hr class="md:hidden w-full border-neutral-300">


                <!--region Address -->
                <div class="flex flex-col gap-4 w-full">

                    <!-- Street address & apartment/suite -->
                    <div class="flex flex-col gap-2">
                        <h3 class="text-lg font-semibold text-neutral-400">Address</h3>
                        <div class="flex gap-2">
                            <input type="text" class="fest-input w-full" placeholder="Street Address"
                                   name="street_name"
                                   aria-label="Optional street address text input">
                            <input type="text" class="fest-input w-16" placeholder="Nr" name="street_number"
                                   aria-label="Optional house number text input">
                        </div>
                        <input type="text" class="fest-input" placeholder="Apartment/Suite" name="apartment_suite"
                               aria-label="Optional apartment or suite number text input">
                    </div>

                    <!-- City & postal code -->
                    <div class="flex flex-col gap-2">
                        <input type="text" class="fest-input" placeholder="City" name="city"
                               aria-label="Optional city text input">
                        <input type="text" class="fest-input" placeholder="Postal Code" name="postal_code"
                               aria-label="Optional postal code text input">
                    </div>

                    <!-- ReCaptcha -->
                    <div class="flex justify-center w-full md:col-span-2">
                        <?php if (!empty($_ENV['RECAPTCHA_SITE_KEY'])): ?>
                            <div class="g-recaptcha" data-sitekey="<?= $_ENV['RECAPTCHA_SITE_KEY'] ?>"></div>
                        <?php else: ?>
                            <p class="text-red-500 text-sm">ReCaptcha configuration missing. Please contact administrator.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!--endregion -->

                <button type="submit" class="fest-btn w-full mt-4 md:col-span-2">Enter</button>
            </form>
        </section>
        <p class="text-neutral-400">Have an account?
            <a href="/login" class="text-black underline">Log in</a></p>
    </section>
</main>
</body>