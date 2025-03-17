<?php $__env->startSection('title', 'Password Reset Token'); ?>

<?php $__env->startSection('content'); ?>

    <p class="mt-2 leading-loose text-gray-600 dark:text-gray-300">
        We have received your request to reset your password.
    </p>

    <p class="mt-2 leading-loose text-gray-600 dark:text-gray-300">
        Your password reset token is:
    </p>


    <div class="flex items-center mt-4 gap-x-4">
        <?php $__currentLoopData = str_split($token); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $digit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <p class="flex items-center justify-center w-10 h-10 text-2xl font-medium beamer-primary border rounded-md">
                <?php echo e($digit); ?></p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <p class="mt-4 leading-loose text-gray-600 dark:text-gray-300">
        Use this token to reset your password, and keep it confidential.
    </p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mails.layouts.default', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/redbillertechnologies/Herd/api.kosa/resources/views/mails/password_reset_token.blade.php ENDPATH**/ ?>