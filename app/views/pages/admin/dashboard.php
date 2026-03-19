<body class="fest-body">
<?php include_once __DIR__ . "/../../layouts/sidebar.php"; ?>
<main class="fest-main">
    
    <section class="fest-container w-full max-w-6xl">
        <header class="w-full flex flex-col justify-start items-start gap-4 border-0">
            <h1 class="text-4xl font-bold">Admin Dashboard</h1>
            <p class="text-neutral-400">Welcome back, Administrator</p>
        </header>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 w-full mt-8">
            <!-- Total Registered Users Card -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">Total Users</h3>
                <p class="text-3xl font-bold text-blue-600"><?= $totalRegisteredUsers ?? 0 ?></p>
                <p class="text-sm text-blue-500 mt-2">Registered users</p>
            </div>

            <!-- Active Users Card -->
            <div class="bg-cyan-50 border border-cyan-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-cyan-900 mb-2">Active Users</h3>
                <p class="text-3xl font-bold text-cyan-600"><?= $activeUsers ?? 0 ?></p>
                <p class="text-sm text-cyan-500 mt-2">Non-banned users</p>
            </div>

            <!-- Active Orders Card -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-green-900 mb-2">Active Orders</h3>
                <p class="text-3xl font-bold text-green-600"><?= $activeOrders ?? 0 ?></p>
                <p class="text-sm text-green-500 mt-2">Pending orders</p>
            </div>

            <!-- Revenue Card -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-purple-900 mb-2">Total Revenue</h3>
                <p class="text-3xl font-bold text-purple-600">€<?= number_format($monthlyRevenue ?? 0, 2, ',', '.') ?></p>
                <p class="text-sm text-purple-500 mt-2">This month</p>
            </div>
        </div>

        <!-- Admin Actions Section -->
        <div class="w-full mt-8 border-t pt-8">
            <h2 class="text-2xl font-bold mb-6">Quick Actions</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- User Management -->
                <a href="/admin/users" class="block bg-white border border-neutral-200 rounded-lg p-6 hover:shadow-md transition">
                    <h3 class="text-lg font-semibold mb-2">📋 Manage Users</h3>
                    <p class="text-neutral-500 text-sm">View, edit, and manage user accounts</p>
                </a>

                <!-- Order Management -->
                <a href="#" class="block bg-white border border-neutral-200 rounded-lg p-6 hover:shadow-md transition">
                    <h3 class="text-lg font-semibold mb-2">📦 Manage Orders</h3>
                    <p class="text-neutral-500 text-sm">Track and process orders</p>
                </a>

                <!-- Content Management -->
                <a href="/admin/homepage" class="block bg-white border border-neutral-200 rounded-lg p-6 hover:shadow-md transition">
                    <h3 class="text-lg font-semibold mb-2">✏️ Edit Homepage</h3>
                    <p class="text-neutral-500 text-sm">Manage homepage content and layout</p>
                </a>

                <!-- System Settings -->
                <a href="#" class="block bg-white border border-neutral-200 rounded-lg p-6 hover:shadow-md transition">
                    <h3 class="text-lg font-semibold mb-2">⚙️ System Settings</h3>
                    <p class="text-neutral-500 text-sm">Configure system settings</p>
                </a>
            </div>
        </div>
    </section>
</main>
</body>
