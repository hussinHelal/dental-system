<?php $transaction = $transaction ?? null; ?>

<div class="modal fade" id="editTransactionModal<?php echo e($transaction->id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo e(route('finance.update', $transaction)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(__('finance.edit_transaction')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <?php echo $__env->make('finance.partials.fields', ['transaction' => $transaction], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?php echo e(__('finance.cancel')); ?>

                    </button>
                    <button type="submit" class="btn btn-primary">
                        <?php echo e(__('finance.save')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/finance/partials/edit-modal.blade.php ENDPATH**/ ?>