<h2>Payment Result</h2>
<?php if ($status === 'lunas'): ?>
    <p>Thank you! Your payment is complete.</p>
<?php elseif ($status === 'belum lunas'): ?>
    <p>Your payment is pending. Please complete the payment or try again.</p>
<?php elseif ($status === 'batal'): ?>
    <p>Your payment was cancelled.</p>
<?php else: ?>
    <p>Unable to determine payment status.</p>
<?php endif; ?>
