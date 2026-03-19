<body class="fest-body">
<?php include_once __DIR__ . "/../layouts/sidebar.php"; ?>
<main class="fest-main">
    <!-- Centered grid -->
    <div class="grid grid-cols-2 gap-6 w-full max-w-4xl mx-auto">
        <!-- Dance Theme -->
        <a href="/dance"
           class="group flex items-center justify-center h-64 bg-dance-600 hover:bg-dance-700 transition-colors rounded-lg shadow-lg">
            <span class="text-4xl font-bold text-white group-hover:scale-110 transition-transform">
                Dance
            </span>
        </a>

        <!-- History Theme -->
        <a href="/history"
           class="group flex items-center justify-center h-64 bg-amber-800 hover:bg-amber-900 transition-colors rounded-lg shadow-lg">
            <span class="text-4xl font-bold text-white group-hover:scale-110 transition-transform">
                History
            </span>
        </a>

        <!-- Jazz Theme -->
        <a href="/jazz"
           class="group flex items-center justify-center h-64 bg-purple-600 hover:bg-purple-700 transition-colors rounded-lg shadow-lg">
            <span class="text-4xl font-bold text-white group-hover:scale-110 transition-transform">
                Jazz
            </span>
        </a>

        <!-- Yummy Theme -->
        <a href="/yummy"
           class="group flex items-center justify-center h-64 bg-green-600 hover:bg-green-700 transition-colors rounded-lg shadow-lg">
            <span class="text-4xl font-bold text-white group-hover:scale-110 transition-transform">
                Yummy
            </span>
        </a>
    </div>

    <!-- Floating logo in the center -->
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-full p-8 shadow-2xl">
        <img src="/assets/icons/logo/logoT-000.svg" alt="Haarlem Festival Logo" class="w-32 h-32">
    </div>
</main>

</body>