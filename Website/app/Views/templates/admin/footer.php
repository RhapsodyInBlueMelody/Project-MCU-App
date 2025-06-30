</div>
</div>
</div>

<script>
// Auto-hide alerts after 5 seconds
setTimeout(function() {
const alerts = document.querySelectorAll('[id^="alert-"]');
alerts.forEach(function(alert) {
    alert.classList.add('opacity-0', 'transition-opacity', 'duration-500');
    setTimeout(() => {
        alert.style.display = 'none';
    }, 500);
});
}, 5000);

// Dismiss alert buttons
document.querySelectorAll('[data-dismiss-target]').forEach(button => {
button.addEventListener('click', () => {
    const target = document.querySelector(button.getAttribute('data-dismiss-target'));
    if (target) {
        target.classList.add('opacity-0', 'transition-opacity', 'duration-500');
        setTimeout(() => {
            target.style.display = 'none';
        }, 500);
    }
});
});
</script>

<!-- Page specific script -->
<?php if (isset($scripts)): ?>
<?php foreach ($scripts as $script): ?>
<script src="<?= base_url($script) ?>"></script>
<?php endforeach; ?>
<?php endif; ?>
</body>
</html>
