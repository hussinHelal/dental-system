<?php $case = $case ?? null; ?>

<div class="modal fade" id="editCaseModal<?php echo e($case->id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo e(route('lab-cases.update', $case)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(__('dental_labs.edit_case')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <?php echo $__env->make('lab-cases.partials.fields', ['case' => $case, 'labs' => $labs], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?php echo e(__('dental_labs.cancel')); ?>

                    </button>
                    <button type="submit" class="btn btn-primary">
                        <?php echo e(__('dental_labs.save')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/lab-cases/partials/edit-modal.blade.php ENDPATH**/ ?>