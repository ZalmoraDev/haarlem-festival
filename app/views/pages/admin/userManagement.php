<?php use app\core\Csrf; ?>
<body class="fest-body">
    <?php include_once __DIR__ . "/../../layouts/sidebar.php"; ?>
    <main class="fest-main">

        <section class="fest-container w-full max-w-7xl">
            <header class="w-full flex flex-col justify-start items-start gap-4 border-0 mb-6">
                <div class="flex items-center justify-between w-full">
                    <div>
                        <h1 class="text-4xl font-bold">User Management</h1>
                        <p class="text-neutral-400">Manage users, permissions, and access</p>
                    </div>
                    <a href="/admin/dashboard"
                        class="px-4 py-2 bg-neutral-200 text-neutral-800 rounded hover:bg-neutral-300 transition">
                        ← Back to Dashboard
                    </a>
                </div>
            </header>

            <!-- Search & Filter Section -->
            <div class="w-full bg-white border border-neutral-200 rounded-lg p-6 mb-6">
                <form method="GET" action="/admin/users" class="flex gap-4 flex-wrap items-end">
                    <!-- Search Input -->
                    <div class="flex-1 min-w-48">
                        <label for="search" class="block text-sm font-medium text-neutral-700 mb-2">Search Users</label>
                        <input type="text" id="search" name="search" value="<?= htmlspecialchars($search ?? '') ?>"
                            placeholder="Search by email, name..."
                            class="w-full px-4 py-2 border border-neutral-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <!-- Sort By -->
                    <div class="min-w-48">
                        <label for="sort_by" class="block text-sm font-medium text-neutral-700 mb-2">Sort By</label>
                        <select name="sort_by" id="sort_by"
                            class="w-full px-4 py-2 border border-neutral-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="created_at" <?= ($sortBy === 'created_at') ? 'selected' : '' ?>>Registration
                                Date</option>
                            <option value="email" <?= ($sortBy === 'email') ? 'selected' : '' ?>>Email</option>
                            <option value="name" <?= ($sortBy === 'name') ? 'selected' : '' ?>>Name</option>
                            <option value="role" <?= ($sortBy === 'role') ? 'selected' : '' ?>>Role</option>
                        </select>
                    </div>

                    <!-- Sort Direction -->
                    <div class="min-w-32">
                        <label for="sort_dir" class="block text-sm font-medium text-neutral-700 mb-2">Order</label>
                        <select name="sort_dir" id="sort_dir"
                            class="w-full px-4 py-2 border border-neutral-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="DESC" <?= ($sortDir === 'DESC') ? 'selected' : '' ?>>Newest First</option>
                            <option value="ASC" <?= ($sortDir === 'ASC') ? 'selected' : '' ?>>Oldest First</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition font-medium">
                        Search
                    </button>
                    <a href="/admin/users"
                        class="px-6 py-2 bg-neutral-300 text-neutral-800 rounded hover:bg-neutral-400 transition font-medium">
                        Clear
                    </a>
                </form>
            </div>

            <!-- Users Table -->
            <div class="w-full bg-white border border-neutral-200 rounded-lg overflow-hidden">
                <?php if (empty($users)): ?>
                    <div class="p-8 text-center text-neutral-500">
                        <p class="text-lg">No users found</p>
                        <p class="text-sm mt-2">Try adjusting your search criteria</p>
                    </div>
                <?php else: ?>
                    <table class="w-full">
                        <thead class="bg-neutral-100 border-b border-neutral-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-neutral-700">Email</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-neutral-700">Name</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-neutral-700">Role</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-neutral-700">Registered</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-neutral-700">Status</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-neutral-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr class="border-b border-neutral-100 hover:bg-neutral-50 transition">
                                    <td class="px-6 py-4 text-sm text-neutral-700">
                                        <span class="font-medium"><?= htmlspecialchars($user['email']) ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-neutral-600">
                                        <?= htmlspecialchars($user['first_name'] ?? '') ?>
                                        <?= htmlspecialchars($user['last_name'] ?? '') ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                        <?php
                                        $role = $user['role'];
                                        if ($role === 'Admin')
                                            echo 'bg-red-100 text-red-800';
                                        elseif ($role === 'Employee')
                                            echo 'bg-purple-100 text-purple-800';
                                        elseif ($role === 'Validated')
                                            echo 'bg-green-100 text-green-800';
                                        else
                                            echo 'bg-blue-100 text-blue-800';
                                        ?>
                                    ">
                                            <?= htmlspecialchars($role) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-neutral-600">
                                        <?= date('d-m-Y', strtotime($user['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-block px-3 py-1 rounded text-xs font-semibold
                                        <?= ($user['is_active']) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>
                                    ">
                                            <?= ($user['is_active']) ? 'Active' : 'Banned' ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm flex gap-2">
                                        <button class="btn-edit px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition"
                                            data-user="<?= $user['id'] ?>">
                                            Edit
                                        </button>
                                        <?php if ($user['is_active']): ?>
                                            <button class="btn-deactivate px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700 transition"
                                                data-user="<?= $user['id'] ?>">
                                                Deactivate
                                            </button>
                                        <?php else: ?>
                                            <button class="btn-reactivate px-3 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700 transition"
                                                data-user="<?= $user['id'] ?>">
                                                Reactivate
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Results Count -->
            <?php if (!empty($users)): ?>
                <div class="mt-4 text-sm text-neutral-600">
                    Showing <?= count($users) ?> user(s)
                </div>
            <?php endif; ?>

        </section>

    </main>

    <script nonce="<?= $csp_nonce ?>">
        document.addEventListener('click', e => {
            const userId = e.target.dataset.user;
            if (!userId) return;

            if (e.target.classList.contains('btn-edit'))
                window.location.href = '/admin/users/' + userId + '/edit';
            else if (e.target.classList.contains('btn-deactivate')) {
                if (confirm('Are you sure you want to deactivate this user?'))
                    submitAction('/admin/users/deactivate', userId);
            }
            else if (e.target.classList.contains('btn-reactivate')) {
                if (confirm('Are you sure you want to reactivate this user?'))
                    submitAction('/admin/users/reactivate', userId);
            }
        });

        function submitAction(action, userId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = action;
            form.innerHTML = `
                <input type="hidden" name="user_id" value="${userId}">
                <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>