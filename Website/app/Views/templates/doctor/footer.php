            </main>
        </div>
    </div>

    <!-- Alpine.js already included in the header -->

    <!-- Page specific script -->
    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= base_url($script) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                alert.style.display = 'none';
            });
        }, 5000);
    </script>
</body>
</html>
