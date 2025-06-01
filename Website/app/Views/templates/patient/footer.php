        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-white shadow-inner mt-auto py-4">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-sm text-gray-600">&copy; <?= date(
                        "Y"
                    ) ?> Medical Check Up. All rights reserved.</p>
                </div>
                <div class="flex space-x-4">
                    <a href="<?= base_url(
                        "privacy-policy"
                    ) ?>" class="text-sm text-gray-600 hover:text-blue-500">Privacy Policy</a>
                    <a href="<?= base_url(
                        "terms-of-service"
                    ) ?>" class="text-sm text-gray-600 hover:text-blue-500">Terms of Service</a>
                    <a href="<?= base_url(
                        "contact-us"
                    ) ?>" class="text-sm text-gray-600 hover:text-blue-500">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Core JavaScript -->
    <script src="<?= base_url("assets/js/patient.min.js") ?>"></script>

    <!-- CSRF protection for AJAX -->
    <script>
        // Attach CSRF token to all AJAX requests
        document.addEventListener('DOMContentLoaded', function() {
            // Add CSRF protection to all AJAX requests
            const xhrOpen = XMLHttpRequest.prototype.open;
            XMLHttpRequest.prototype.open = function() {
                xhrOpen.apply(this, arguments);
                this.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            };

            // Service worker registration for PWA capabilities and offline support
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('<?= base_url(
                        "service-worker.js"
                    ) ?>').then(function(registration) {
                        console.log('ServiceWorker registration successful');
                    }, function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
                });
            }
        });
    </script>

    <!-- Page-specific scripts will be added here -->
    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= base_url($script) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
