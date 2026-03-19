<?php

use app\core\Csrf;
use app\core\Escaper;

/** @var array $viewData /app/Core/View.php View::render */

// Injected by Router::dispatch() via AdminCtrl::editUserPage()
$userId = (int)$viewData['id'] ?? null;
$firstName = Escaper::html($viewData['first_name'] ?? '');
$lastName = Escaper::html($viewData['last_name'] ?? '');
$username = Escaper::html($viewData['username'] ?? '');
$email = Escaper::html($viewData['email'] ?? '');
$phoneNumber = Escaper::html($viewData['phone_number'] ?? '');
$streetName = Escaper::html($viewData['street_name'] ?? '');
$streetNumber = Escaper::html((string)($viewData['street_number'] ?? ''));
$apartmentSuite = Escaper::html($viewData['apartment_suite'] ?? '');
$city = Escaper::html($viewData['city'] ?? '');
$postalCode = Escaper::html($viewData['postal_code'] ?? '');
$role = Escaper::html($viewData['role'] ?? 'Customer');
$createdAt = $viewData['created_at'] ?? '';
$isActive = $viewData['is_active'] ?? true;
?>

<body class="fest-body">
    <?php include_once __DIR__ . "/../../layouts/sidebar.php"; ?>
    <main class="fest-main">
        <section class="fest-container w-full max-w-4xl">
            <header class="w-full flex flex-col justify-start items-start gap-4 border-0 mb-6">
                <div class="flex items-center justify-between w-full">
                    <div>
                        <h1 class="text-4xl font-bold">Edit User</h1>
                        <p class="text-neutral-400">Modify user details and permissions</p>
                    </div>
                    <a href="/admin/users"
                        class="px-4 py-2 bg-neutral-200 text-neutral-800 rounded hover:bg-neutral-300 transition">
                        ← Back to Users
                    </a>
                </div>
            </header>

            <!-- User Information Display -->
            <div class="w-full bg-white border border-neutral-200 rounded-lg p-6 mb-6">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-neutral-500">User ID</p>
                        <p class="text-lg font-semibold"><?= $userId ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-neutral-500">Registered</p>
                        <p class="text-lg font-semibold"><?= date('d-m-Y H:i', strtotime($createdAt)) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-neutral-500">Status</p>
                        <p class="text-lg font-semibold">
                            <span class="inline-block px-3 py-1 rounded text-xs font-semibold
                                <?= $isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= $isActive ? 'Active' : 'Banned' ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Edit User Form -->
            <div class="w-full bg-white border border-neutral-200 rounded-lg p-6 mb-6">
                <h2 class="text-2xl font-bold mb-4">User Details</h2>
                <form action="/admin/users/<?= $userId ?>/edit" method="POST" class="flex flex-col gap-4">
                    <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">

                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label for="first_name" class="block text-sm font-medium text-neutral-700">First Name *</label>
                            <input type="text" id="first_name" name="first_name" 
                                value="<?= $firstName ?>" required
                                class="w-full px-4 py-2 border border-neutral-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                aria-label="First name">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="last_name" class="block text-sm font-medium text-neutral-700">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" 
                                value="<?= $lastName ?>" required
                                class="w-full px-4 py-2 border border-neutral-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                aria-label="Last name">
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label for="username" class="block text-sm font-medium text-neutral-700">Username *</label>
                            <input type="text" id="username" name="username" 
                                value="<?= $username ?>" required
                                class="w-full px-4 py-2 border border-neutral-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                aria-label="Username">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="email" class="block text-sm font-medium text-neutral-700">Email *</label>
                            <input type="email" id="email" name="email" 
                                value="<?= $email ?>" required
                                class="w-full px-4 py-2 border border-neutral-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                aria-label="Email address">
                        </div>
                    </div>

                    <!-- Phone Number -->
                    <div class="flex flex-col gap-2">
                        <label for="phone_number" class="block text-sm font-medium text-neutral-700">Phone Number</label>
                        <input type="tel" id="phone_number" name="phone_number" 
                            value="<?= $phoneNumber ?>"
                            pattern="^\+\d{1,3}[\s\-]?\d{8,15}$"
                            placeholder="+31 6 12345678"
                            class="w-full px-4 py-2 border border-neutral-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                            aria-label="Phone number (optional)">
                        <p class="text-xs text-neutral-400">Format: +31 6 12345678 (optional)</p>
                    </div>

                    <!-- Address -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label for="street_name" class="block text-sm font-medium text-neutral-700">Street Name *</label>
                            <input type="text" id="street_name" name="street_name"
                                value="<?= $streetName ?>" required
                                class="w-full px-4 py-2 border border-neutral-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                aria-label="Street name">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="street_number" class="block text-sm font-medium text-neutral-700">Street Number *</label>
                            <input type="number" id="street_number" name="street_number"
                                value="<?= $streetNumber ?>" required
                                class="w-full px-4 py-2 border border-neutral-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                aria-label="Street number">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-2">
                            <label for="apartment_suite" class="block text-sm font-medium text-neutral-700">Apartment / Suite</label>
                            <input type="text" id="apartment_suite" name="apartment_suite"
                                value="<?= $apartmentSuite ?>"
                                class="w-full px-4 py-2 border border-neutral-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                aria-label="Apartment or suite (optional)">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="city" class="block text-sm font-medium text-neutral-700">City *</label>
                            <input type="text" id="city" name="city"
                                value="<?= $city ?>" required
                                class="w-full px-4 py-2 border border-neutral-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                aria-label="City">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="postal_code" class="block text-sm font-medium text-neutral-700">Postal Code *</label>
                            <input type="text" id="postal_code" name="postal_code"
                                value="<?= $postalCode ?>" required
                                class="w-full px-4 py-2 border border-neutral-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                aria-label="Postal code">
                        </div>
                    </div>

                    <!-- Role Selection -->
                    <div class="flex flex-col gap-2">
                        <label for="role" class="block text-sm font-medium text-neutral-700">Role *</label>
                        <select id="role" name="role" required
                            class="w-full px-4 py-2 border border-neutral-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                            aria-label="User role">
                            <option value="Customer" <?= $role === 'Customer' ? 'selected' : '' ?>>Customer</option>
                            <option value="Validated" <?= $role === 'Validated' ? 'selected' : '' ?>>Validated</option>
                            <option value="Employee" <?= $role === 'Employee' ? 'selected' : '' ?>>Employee</option>
                            <option value="Admin" <?= $role === 'Admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4 justify-end pt-4 border-t">
                        <a href="/admin/users"
                            class="px-6 py-2 bg-neutral-200 text-neutral-800 rounded hover:bg-neutral-300 transition font-medium">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition font-medium">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>
