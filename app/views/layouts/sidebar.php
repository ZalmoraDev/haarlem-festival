<?php

use app\core\Csrf;
use app\core\Escaper;
use app\models\enums\UserRole;

/** @var array $viewData /app/core/View.php View::render */
?>

<!-- Collapsible Sidebar Nav -->
<aside class="group fixed flex flex-col top-0 left-0 w-16 hover:w-64 h-full z-50 bg-white border-r border-neutral-200 transition-all duration-300 ease-in-out">
    <!-- Section: Logo -->
    <div class="flex items-center justify-between h-16 p-4 border-b border-neutral-200">
        <!-- Logo and Site Name -->
        <a href="/" class="flex items-center min-w-0 hover:brightness-50">
            <img src="/assets/icons/logo/logo-000.svg" alt="Haarlem Festival Logo" class="w-8 h-8 fest-nav-item-icon"/>
            <h1 class="opacity-0 group-hover:opacity-100 text-xl ml-2 whitespace-nowrap overflow-hidden transition-opacity duration-300"><?= $_ENV['SITE_NAME'] ?></h1>
        </a>
    </div>

    <!-- Section: Pagenav -->
    <nav class="flex-1 py-4 flex flex-col gap-4 px-2 overflow-y-auto overflow-x-hidden">
        <!-- Category: Themes -->
        <div class="flex flex-col">
            <div class="fest-nav-category">
                <span class="fest-nav-category-short">E</span>
                <span class="fest-nav-category-full">Events</span>
            </div>
            <a href="/dance" class="fest-nav-item group/item">
                <img src="/assets/icons/themes/dance-000.svg" class="fest-nav-item-icon" alt="Loud music icon">
                <span class="fest-nav-item-text">Dance</span>
            </a>
            <a href="/history" class="fest-nav-item group/item">
                <img src="/assets/icons/themes/history-000.svg" class="fest-nav-item-icon" alt="History scroll icon">
                <span class="fest-nav-item-text">History</span>
            </a>
            <a href="/jazz" class="fest-nav-item group/item">
                <img src="/assets/icons/themes/jazz-000.svg" class="fest-nav-item-icon" alt="Jazz trumpet icon">
                <span class="fest-nav-item-text">Jazz</span>
            </a>
            <a href="/yummy" class="fest-nav-item group/item">
                <img src="/assets/icons/themes/yummy-000.svg" class="fest-nav-item-icon" alt="Knife and fork icon">
                <span class="fest-nav-item-text">Yummy</span>
            </a>
        </div>

        <!-- Category: Settings -->
        <div class="flex flex-col">
            <div class="fest-nav-category">
                <span class="fest-nav-category-short">O</span>
                <span class="fest-nav-category-full">Other</span>
            </div>
            <!-- TODO: Create route and page -->
            <a href="/attribution" class="fest-nav-item group/item">
                <img src="/assets/icons/sidebar/attribution-000.svg" class="fest-nav-item-icon"
                     alt="Credited authors icon">
                <span class="fest-nav-item-text">Attribution</span>
            </a>
        </div>
    </nav>

    <!-- Section: User Info -->
    <div class="border-t border-neutral-200 p-2">
        <?php if ($viewData['user']['role'] !== UserRole::Visitor): ?>
            <a href="/cart" class="fest-nav-item group/item">
                <img src="/assets/icons/sidebar/cart-000.svg" class="fest-nav-item-icon" alt="Cart icon">
                <span class="fest-nav-item-text">Cart</span>
            </a>
            <a href="/settings" class="fest-nav-item group/item">
                <img src="/assets/icons/sidebar/account-000.svg" alt="Userprofile icon" class="fest-nav-item-icon">
                <div class="flex flex-col min-w-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <span class="font-medium whitespace-nowrap overflow-hidden text-ellipsis"><?= Escaper::html($viewData['user']['username']) ?></span>
                    <span class="text-sm text-gray-400 whitespace-nowrap"><?= $viewData['user']['role']->value ?></span>
                </div>
            </a>
        <?php endif;
        if ($viewData['user']['role'] === UserRole::Visitor): ?>
            <a href="/login" class="fest-nav-item group/item">
                <img src="/assets/icons/sidebar/logout-000.svg" alt="Login icon" class="fest-nav-item-icon">
                <span class="fest-nav-item-text">Log in</span>
            </a>
        <?php else: ?>
            <form action="/auth/logout" method="POST">
                <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                <button type="submit" class="fest-nav-item group/item w-full cursor-pointer">
                    <img src="/assets/icons/sidebar/logout-000.svg" alt="Logout icon">
                    <span class="fest-nav-item-text">Log out</span>
                </button>
            </form>
        <?php endif; ?>
    </div>
</aside>