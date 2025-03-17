<!-- resources/views/emails/layouts/default.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title'); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <style>
        .beamer-primary {
            color: #00ADB5;
            border-color: #00ADB5;
        }
    </style>
</head>

<body class="bg-gray-100">
    <section class="max-w-2xl px-6 py-8 mx-auto bg-white dark:bg-gray-900">
        <header>
            <a href="#">
                <img class="w-auto h-7 sm:h-8" src="<?php echo e(url('beamer-logo.png')); ?>" alt="<?php echo e(config('app.name')); ?>">
            </a>
        </header>

        <main class="mt-8">
            <?php echo $__env->yieldContent('content'); ?>
            <p class="mt-8 text-gray-600 dark:text-gray-300">
                Regards, <br>
                Kosa Team
            </p>
        </main>

        <footer class="mt-8">
            <p class="text-gray-500 dark:text-gray-400">
                If you'd rather not receive this kind of email, you can <a href="#"
                    class="text-blue-600 hover:underline dark:text-blue-400">unsubscribe</a> or <a href="#"
                    class="text-blue-600 hover:underline dark:text-blue-400">manage your email preferences</a>.
            </p>

            <p class="mt-3 text-gray-500 dark:text-gray-400"> <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. All Rights
                Reserved.</p>
        </footer>
    </section>
</body>

</html>
<?php /**PATH /Users/redbillertechnologies/Herd/api.kosa/resources/views/mails/layouts/default.blade.php ENDPATH**/ ?>