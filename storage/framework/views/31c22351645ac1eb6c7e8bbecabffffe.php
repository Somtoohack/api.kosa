<!-- resources/views/emails/verify-email.blade.php -->


<?php $__env->startSection('title', 'Verify your account'); ?>
<?php $__env->startSection('content'); ?>
    <h2 class="text-gray-700 dark:text-gray-200">Hi <?php echo e($user->name ?? $user->email); ?>,</h2>

    <p class="mt-2 leading-loose text-gray-600 dark:text-gray-300">
        Your account has been temporarily locked due to multiple failed login attempts. To verify your identity and unlock
        your account, please use the following OTP:
    </p>

    <div class="flex items-center mt-4 gap-x-4">
        <?php $__currentLoopData = str_split($otp); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $digit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <p class="flex items-center justify-center w-10 h-10 text-2xl font-medium beamer-primary border rounded-md">
                <?php echo e($digit); ?></p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <p class="mt-4 leading-loose text-gray-600 dark:text-gray-300">
        This code will only be valid for the next 5 minutes.
    </p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mails.layouts.default', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/redbillertechnologies/Herd/api.kosa/resources/views/mails/otp-mail.blade.php ENDPATH**/ ?>