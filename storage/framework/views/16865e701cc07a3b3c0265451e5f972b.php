<?php $supplier = $supplier ?? null; ?>

<div class="modal fade" id="editSupplierModal<?php echo e($supplier->id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo e(route('suppliers.update', $supplier)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(__('suppliers.edit_supplier')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"><?php echo $__env->make('suppliers.partials.fields', ['supplier' => $supplier], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo e(__('suppliers.cancel')); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo e(__('suppliers.save')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/suppliers/partials/edit-modal.blade.php ENDPATH**/ ?>