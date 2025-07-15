<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Result</title>
    <link href="/css/style.css" rel="stylesheet">
</head>

<body class="bg-gray-100 text-gray-800 font-sans leading-normal tracking-normal">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl max-w-md">
        <h2 class="text-3xl font-bold text-blue-700 border-b-2 border-gray-200 pb-4 mb-6">Payment Result</h2>

        <?php if (isset($invoice_number) && $invoice_number): ?>
            <p class="text-lg mb-4"><strong>Invoice Number:</strong> <span class="font-medium text-gray-700"><?php echo htmlspecialchars($invoice_number); ?></span></p>
        <?php else: ?>
            <p class="text-lg text-gray-600 mb-4">No invoice number was provided.</p>
        <?php endif; ?>

        <?php if (isset($status)): ?>
            <?php if ($status === 'lunas'): ?>
                <p class="text-2xl text-green-600 font-semibold mb-4">Thank you! Your payment is complete.</p>
            <?php elseif ($status === 'pending'): ?>
                <p class="text-2xl text-yellow-600 font-semibold mb-4">Your payment is pending. Please complete the payment or check back later.</p>
            <?php elseif ($status === 'batal'): ?>
                <p class="text-2xl text-red-600 font-semibold mb-4">Your payment was cancelled.</p>
            <?php else: ?>
                <p class="text-2xl text-gray-500 font-semibold mb-4">Unable to determine payment status.</p>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-2xl text-gray-500 font-semibold mb-4">Payment status could not be retrieved.</p>
        <?php endif; ?>

        <?php if (isset($message) && $message): ?>
            <?php if (isset($updated_by_result_page) && $updated_by_result_page): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Warning!</strong>
                    <span class="block sm:inline"><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php else: ?>
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <a href="/" class="inline-block mt-6 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-300 ease-in-out shadow-md">
            Back to Merchant Site
        </a>
    </div>
</body>

</html>
