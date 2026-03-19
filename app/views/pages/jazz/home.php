<body>
<?php include_once __DIR__ . "/../../layouts/sidebar.php"; ?>
<main class="flex flex-col min-h-screen">

    <!-- Hero Section -->
    <section class="w-full bg-jazz-yellow-500 text-white py-16 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <img src="/assets/icons/logo/logo-FFF-white.svg" alt="FFF Logo" class="w-24 h-24 mb-8 mx-auto">

            <h1 class="text-5xl font-bold mb-8">Haarlem Jazz Festival</h1>

            <p class="text-lg mb-12 max-w-2xl mx-auto">
                Get ready for an unforgettable weekend filled with soulful melodies, vibrant rhythms,
                and world-class musicians! Whether you’re here to experience jazz legends or discover
                rising stars, Haarlem Jazz offers you four days of nonstop music and magic across the
                historic streets and stunning venues of Haarlem.
            </p>

            <!-- Two Artist Cards (preview) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                <div class="bg-white text-gray-800 p-6 rounded-lg shadow-lg">
                    <h3 class="text-2xl font-bold mb-2">Miles Davis Tribute</h3>
                    <p class="text-gray-600 mb-4">Vrijdag, 19:00</p>
                    <p class="text-sm mb-4">Grote Zaal, Haarlem</p>
                    <button class="bg-jazz-yellow-500 text-white px-6 py-2 rounded font-semibold hover:bg-jazz-yellow-500">
                        Meer info
                    </button>
                </div>

                <div class="bg-white text-gray-800 p-6 rounded-lg shadow-lg">
                    <h3 class="text-2xl font-bold mb-2">Nina Simone Sessions</h3>
                    <p class="text-gray-600 mb-4">Zaterdag, 20:30</p>
                    <p class="text-sm mb-4">Theaterzaal, Haarlem</p>
                    <button class="bg-jazz-yellow-500 text-white px-6 py-2 rounded font-semibold hover:bg-jazz-yellow-500">
                        Meer info
                    </button>
                </div>
            </div>

            <!-- Arrow -->
            <a href="#artists" class="inline-block hover:opacity-80 transition">
                <svg class="w-8 h-8 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </a>
        </div>
    </section>

    <!-- Artists Section -->
    <section id="artists" class="w-full bg-jazz-yellow-500 py-16">

        <!-- Purple Title -->
        <div class="bg-jazz-purple-500 py-12 mb-16">
            <h2 class="text-4xl font-bold text-white text-center">
                Artists
            </h2>
        </div>

        <!-- Content -->
        <div class="max-w-4xl mx-auto px-4 text-gray-800">

            <!-- Intro -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-16">
                <img src="/assets/icons/logo/logo-FFF-white.svg" alt="Trompet" class="w-full rounded-lg">

                <div>
                    <h3 class="text-3xl font-bold mb-4">Haarlem Jazz</h3>
                    <p class="text-lg mb-4">
                        Immerse yourself in the rich traditions of jazz, from smooth swing to lively bebop,
                        and explore a lineup that celebrates the diversity of this iconic genre.
                    </p>
                    <p class="text-lg">
                        Explore the schedule, enjoy the atmosphere, and let Haarlem Jazz ignite your
                        passion for music.
                    </p>
                </div>
            </div>

            <!-- Artist Cards (later component) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">

                <div class="artist-card bg-jazz-purple-500 text-white p-6 rounded-lg shadow-lg">
                    <div class="w-full h-48 bg-gray-300 rounded mb-4"></div>
                    <h4 class="text-xl font-bold mb-2">John Coltrane</h4>
                    <p class="text-sm mb-2">Saxofoon</p>
                    <p class="text-sm">Vrijdag 19:00</p>
                </div>

                <div class="artist-card bg-jazz-purple-500 text-white p-6 rounded-lg shadow-lg">
                    <div class="w-full h-48 bg-gray-300 rounded mb-4"></div>
                    <h4 class="text-xl font-bold mb-2">Ella Fitzgerald</h4>
                    <p class="text-sm mb-2">Zang</p>
                    <p class="text-sm">Zaterdag 20:30</p>
                </div>

                <div class="artist-card bg-jazz-purple-500 text-white p-6 rounded-lg shadow-lg">
                    <div class="w-full h-48 bg-gray-300 rounded mb-4"></div>
                    <h4 class="text-xl font-bold mb-2">Dizzy Gillespie</h4>
                    <p class="text-sm mb-2">Trompet</p>
                    <p class="text-sm">Zaterdag 22:00</p>
                </div>

                <div class="artist-card bg-jazz-purple-500 text-white p-6 rounded-lg shadow-lg">
                    <div class="w-full h-48 bg-gray-300 rounded mb-4"></div>
                    <h4 class="text-xl font-bold mb-2">Billie Holiday</h4>
                    <p class="text-sm mb-2">Zang</p>
                    <p class="text-sm">Zondag 19:00</p>
                </div>

            </div>

            <!-- Arrow -->
            <div class="flex justify-center">
                <a href="#schedule" class="text-gray-800 hover:opacity-80 transition">
                    <svg class="w-8 h-8 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                </a>
            </div>

        </div>
    </section>

    <!-- Schedule Section -->
    <section id="schedule" class="w-full bg-jazz-yellow-500 py-16">

        <!-- Purple Title -->
        <div class="bg-jazz-purple-500 py-12 mb-16">
            <h2 class="text-4xl font-bold text-white text-center">
                Schema
            </h2>
        </div>

        <!-- Timetable -->
        <div class="max-w-4xl mx-auto px-4 text-white space-y-8">

            <!-- Day Block (later component) -->
            <div class="timetable-day bg-jazz-purple-500 rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4">Donderdag</h3>

                <div class="space-y-3">
                    <div class="flex justify-between border-b border-purple-600 pb-3">
                        <div>
                            <p class="font-semibold">Miles Davis Tribute</p>
                            <p class="text-sm text-gray-300">Grote Zaal</p>
                        </div>
                        <p class="font-semibold">19:00</p>
                    </div>

                    <div class="flex justify-between">
                        <div>
                            <p class="font-semibold">Jazz Fusion Night</p>
                            <p class="text-sm text-gray-300">Theaterzaal</p>
                        </div>
                        <p class="font-semibold">21:00</p>
                    </div>
                </div>
            </div>

            <div class="timetable-day bg-jazz-purple-500 rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4">Vrijdag</h3>
                <!-- items -->
            </div>

            <div class="timetable-day bg-jazz-purple-500 rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4">Zaterdag</h3>
                <!-- items -->
            </div>

            <div class="timetable-day bg-jazz-purple-500 rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4">Zondag</h3>
                <!-- items -->
            </div>

        </div>
    </section>

</main>

</body>