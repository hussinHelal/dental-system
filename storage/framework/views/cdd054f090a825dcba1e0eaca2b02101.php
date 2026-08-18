<div class="modal fade" id="createInsuranceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo e(route('insurance.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(__('insurance.add_contract')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <?php echo $__env->make('insurance.partials.fields', ['contract' => null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?php echo e(__('insurance.cancel')); ?>

                    </button>
                    <button type="submit" class="btn btn-primary">
                        <?php echo e(__('insurance.save')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/insurance/partials/create-modal.blade.php ENDPATH**/ ?>