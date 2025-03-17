<?php $__env->startSection('title', $subject); ?>

<?php $__env->startSection('content'); ?>
    <p style="font-size:1rem;line-height:1.5rem;margin:16px 0">
        <?php echo $body; ?>

    </p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mails.layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/redbillertechnologies/Herd/api.kosa/resources/views/mails/trans_notification.blade.php ENDPATH**/ ?>