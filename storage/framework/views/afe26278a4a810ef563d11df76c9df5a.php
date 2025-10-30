<!--[if BLOCK]><![endif]--><?php if(Session::has('success')): ?>
<div class="w-full bg-green-300 text-green-700 px-3 py-3 rounded-md border-green-500 mb-4">
<?php echo e(Session::get('success')); ?>

</div>
<?php endif; ?><!--[if ENDBLOCK]><![endif]-->

<!--[if BLOCK]><![endif]--><?php if(Session::has('error')): ?>
<div class="w-full bg-red-300 text-red-700 px-3 py-3 rounded-md border-red-400 mb-4">
<?php echo e(Session::get('error')); ?>

</div>
<?php endif; ?><!--[if ENDBLOCK]><![endif]--><?php /**PATH /Users/prayoga/Website Adek/daftar-proyek-main/resources/views/components/message.blade.php ENDPATH**/ ?>