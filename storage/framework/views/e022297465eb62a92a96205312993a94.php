<?php $purchase = $purchase ?? null; ?>

<div class="modal fade" id="editPurchaseModal<?php echo e($purchase->id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo e(route('purchases.update', $purchase)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(__('purchases.edit_purchase')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <?php echo $__env->make('purchases.partials.fields', [
                        'purchase' => $purchase,
                        'suppliers' => $suppliers,
                        'tableId' => 'itemsTableEdit' . $purchase->id,
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?php echo e(__('purchases.cancel')); ?>

                    </button>
                    <button type="submit" class="btn btn-primary">
                        <?php echo e(__('purchases.save')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php $__currentLoopData = $purchase->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            addPurchaseItemRow(
                'itemsTableEdit<?php echo e($purchase->id); ?>',
                <?php echo json_encode($item->item_name, 15, 512) ?>,
                <?php echo e($item->quantity); ?>,
                <?php echo e($item->unit_price); ?>

            );
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    });
</script>
<?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/purchases/partials/edit-modal.blade.php ENDPATH**/ ?>