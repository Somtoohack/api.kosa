<?php $__env->startSection('title', 'Your password was changed'); ?>

<?php $__env->startSection('content'); ?>

    <p class="mt-2 leading-loose text-gray-600 dark:text-gray-300">
        Your password has been changed successfully.
    </p>


    <p class="mt-4 leading-loose text-gray-600 dark:text-gray-300">
        If you did not request this change, please contact support immediately.
    </p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mails.layouts.default', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/redbillertechnologies/Herd/api.kosa/resources/views/mails/password_reset_confirmation.blade.php ENDPATH**/ ?>