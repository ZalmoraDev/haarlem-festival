<?php

use App\Core\Csrf;
use App\Core\Escaper;

/** @var array $viewData /app/Core/View.php View::render */


$userId = (int)$viewData['user']['id'] ?? null;
$firstName = Escaper::html($viewData['user']['firstName']) ?? null;
$lastName = Escaper::html($viewData['user']['lastName']) ?? null;
$username = Escaper::html($viewData['user']['username']) ?? null;
$email = Escaper::html($viewData['user']['email']) ?? null;
$phoneNumber = Escaper::html($viewData['user']['phoneNumber']) ?? null;
$streetName = Escaper::html($viewData['user']['streetName'] ?? '');
$streetNumber = Escaper::html((string)($viewData['user']['streetNumber'] ?? ''));
$apartmentSuite = Escaper::html($viewData['user']['apartmentSuite'] ?? '');
$city = Escaper::html($viewData['user']['city'] ?? '');
$postalCode = Escaper::html($viewData['user']['postalCode'] ?? '');
?>

<body class="fest-body flex flex-col">

<?php include_once __DIR__ . "/../../layouts/sidebar.php"; ?>

<main class="flex-1 flex flex-col gap-10 w-full max-w-full justify-center items-center overflow-y-auto relative mb-4">
    <section class="flex flex-col gap-6">
        <header class="fest-container p-2 text-2xl w-full max-w-full mt-4">
            <h1>Settings</h1>
        </header>
        <!-- Account sections -->
        <article class="flex flex-col gap-4">
            <!-- Edit Account -->
            <section class="fest-container gap-4 flex flex-col w-full">
                <h2 class="text-2xl">Edit account</h2>
                <form action="/user/edit" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                    <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">

                    <!--region Identity and password -->
                    <div class="flex flex-col gap-4 w-full">

                        <!-- Name -->
                        <div class="flex flex-col gap-2">
                            <h3 class="text-lg font-semibold text-neutral-400">Name</h3>
                            <input type="text" class="fest-input" placeholder="First name *" name="first_name"
                                   value="<?= $firstName ?>" required
                                   aria-label="Required first name text input">
                            <input type="text" class="fest-input" placeholder="Last name *" name="last_name"
                                   value="<?= $lastName ?>" required
                                   aria-label="Required last name text input">
                            <input type="text" class="fest-input" placeholder="Username [3-32] *" name="username"
                                   value="<?= $username ?>" required
                                   aria-label="Required username text input, 3 to 32 characters">
                        </div>

                        <!-- Contact -->
                        <div class="flex flex-col gap-2">
                            <h3 class="text-lg font-semibold text-neutral-400">Contact</h3>
                            <input type="email" class="fest-input" placeholder="Email *" name="email"
                                   value="<?= $email ?>" required
                                   aria-label="Required Email address text input, must be a valid email format">
                            <!-- TODO (STEF): Further verify if format is correct, also maybe add dependency for handling phonenumber input, here and backend-->
                            <input type="tel" class="fest-input" placeholder="Phone-number (e.g. +31 6 12345678)"
                                   name="phone_number" value="<?= $phoneNumber ?>"
                                   pattern="^\+\d{1,3}[\s\-]?\d{8,15}$"
                                   title="Must start with + followed by country code (1-3 digits) and phone number (8-15 digits)"
                                   aria-label="Optional phone-number text input, must be a valid international phone number format starting with +"
                                   autocomplete="tel">
                        </div>

                        <!-- Password -->
                        <div class="flex flex-col gap-2">
                            <h3 class="text-lg font-semibold text-neutral-400">Password</h3>
                            <input type="password" class="fest-input" placeholder="Password *" name="password"
                                   minlength="12"
                                   title="Must have: 1 lowercase, 1 uppercase, 1 digit, no spaces, 12-64 characters"
                                   pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])\S{12,64}$"
                                   aria-label="Required password text input. Must have 1 lowercase, 1 uppercase, 1 digit, no spaces, 12-64 characters">
                            <input type="password" class="fest-input" placeholder="Confirm Password *"
                                   name="password_confirm"
                                   minlength="12" aria-label="Required confirm password text input">
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
                                       value="<?= $streetName ?>"
                                       aria-label="Optional street address text input">
                                <input type="text" class="fest-input w-16" placeholder="Nr" name="street_number"
                                       value="<?= $streetNumber ?>"
                                       aria-label="Optional house number text input">
                            </div>
                            <input type="text" class="fest-input" placeholder="Apartment/Suite" name="apartment_suite"
                                   value="<?= $apartmentSuite ?>"
                                   aria-label="Optional apartment or suite number text input">
                        </div>

                        <!-- City & postal code -->
                        <div class="flex flex-col gap-2">
                            <input type="text" class="fest-input" placeholder="City" name="city"
                                   value="<?= $city ?>"
                                   aria-label="Optional city text input">
                            <input type="text" class="fest-input" placeholder="Postal Code" name="postal_code"
                                   value="<?= $postalCode ?>"
                                   aria-label="Optional postal code text input">
                        </div>
                        <p class="text-xs text-neutral-400 w-full text-left mt-1">
                            * Required fields
                        </p>
                    </div>
                    <!--endregion -->

                    <button type="submit" class="fest-btn w-full mt-4 md:col-span-2">Confirm edit</button>
                </form>
            </section>

            <!-- Delete Account -->
            <section class="fest-container gap-4 flex flex-col w-full">
                <h2 class="text-2xl">Delete account</h2>
                <form action="/user/delete" method="POST" class="flex flex-col gap-4 w-full">
                    <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                    <p class="mb-2">Repeat account name to confirm deletion:</p>
                    <input type="text" class="fest-input w-full" placeholder="Account Name"
                           name="confirm_username"
                           required aria-label="Confirm account name for deletion">
                    <button type="submit"
                            class="cursor-pointer fest-btn bg-red-600 hover:bg-red-700 text-white font-bold w-full">
                        CONFIRM DELETION
                    </button>
                </form>
            </section>
        </article>
    </section>
</main>
</body>