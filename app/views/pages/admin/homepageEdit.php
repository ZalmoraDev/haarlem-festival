<?php
use app\core\Csrf;
?>

<body class="fest-body">
<?php include_once __DIR__ . "/../../layouts/sidebar.php"; ?>
<main class="fest-main">
    
    <section class="fest-container w-full max-w-6xl">
        <header class="w-full flex flex-col justify-start items-start gap-4 border-0">
            <h1 class="text-4xl font-bold">Edit Homepage Content</h1>
            <p class="text-neutral-400">Edit the HTML content displayed on the homepage</p>
        </header>

        <!-- Edit Form -->
        <div class="w-full mt-8">
            <form method="post" action="/admin/homepage" class="flex flex-col gap-6">
                <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                <!-- Content Editor Section -->
                <div class="flex flex-col gap-3">
                    <label for="content" class="text-lg font-semibold text-neutral-800">
                        Homepage Content
                    </label>
                    <p class="text-sm text-neutral-500 mb-2">
                        Allowed HTML tags: &lt;p&gt;, &lt;br&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;u&gt;, &lt;h1-h6&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;li&gt;, &lt;a&gt;, &lt;img&gt;, &lt;div&gt;, &lt;span&gt;
                    </p>
                    <textarea 
                        id="content" 
                        name="content" 
                        class="w-full min-h-96 p-4 border-2 border-neutral-300 rounded-lg font-mono text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                        placeholder="Enter your HTML content here...&#10;&#10;Example:&#10;&lt;h1&gt;Welcome to Haarlem Festival&lt;/h1&gt;&#10;&lt;p&gt;This is your homepage content.&lt;/p&gt;"
                        required
                    ><?php echo htmlspecialchars($content ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 justify-end">
                    <a href="/admin/dashboard" class="px-6 py-3 bg-neutral-200 hover:bg-neutral-300 text-neutral-800 font-semibold rounded-lg transition">
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition"
                    >
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Preview Section (Optional) -->
        <div class="w-full mt-12 border-t pt-8">
            <h2 class="text-2xl font-bold mb-4">Preview</h2>
            <div class="bg-white border border-neutral-200 rounded-lg p-6">
                <div id="preview" class="prose prose-sm max-w-none">
                    <?php echo $content ?? '<p class="text-neutral-500">No content yet. Add content above to see preview.</p>'; ?>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="w-full mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-2">💡 Tips</h3>
            <ul class="text-blue-800 text-sm space-y-2 list-disc list-inside">
                <li>Use HTML tags to format your content</li>
                <li>Unsafe tags will be automatically removed for security</li>
                <li>Test your content in the preview section below before saving</li>
                <li>The homepage will display this content dynamically for all visitors</li>
            </ul>
        </div>
    </section>
</main>

<script>
    // Simple live preview (optional enhancement)
    const contentTextarea = document.getElementById('content');
    const previewDiv = document.getElementById('preview');

    contentTextarea.addEventListener('input', function () {
        const content = this.value;
        if (content.trim())
            previewDiv.innerHTML = content;
        else
            previewDiv.innerHTML = '<p class="text-neutral-500">No content yet. Add content above to see preview.</p>';
    });
</script>
</body>
