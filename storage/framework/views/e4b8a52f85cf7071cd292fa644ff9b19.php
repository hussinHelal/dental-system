<?php $__env->startSection('title', __('insurance.title')); ?>

<?php $__env->startSection('content'); ?>
    <script src="<?php echo e(asset('js/dental-ui.js')); ?>" defer></script>
    <div class="container-fluid px-0">
        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <ul class="mb-0 ps-3">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3 shadow-sm p-3 rounded-4 zedan-page-header">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary-subtle p-2"><i class="bi bi-shield-check text-primary"></i></div>
                <h3 class="mb-0"><?php echo e(__('insurance.title')); ?></h3>
            </div>
            <form method="GET" class="d-flex flex-wrap gap-2 flex-grow-1 justify-content-end">
                <div class="input-group" style="max-width: 290px;">
                    <span class="input-group-text bg-body"><i class="bi bi-search"></i></span>
                    <input id="insuranceSearch" type="search" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="<?php echo e(__('insurance.search')); ?>" maxlength="100" autocomplete="off">
                </div>
                <select id="insuranceStatus" name="status" class="form-select" style="max-width: 180px;">
                    <option value=""><?php echo e(__('insurance.all_statuses')); ?></option>
                    <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>><?php echo e(__('insurance.status_active')); ?></option>
                    <option value="expired" <?php if(request('status') === 'expired'): echo 'selected'; endif; ?>><?php echo e(__('insurance.status_expired')); ?></option>
                    <option value="cancelled" <?php if(request('status') === 'cancelled'): echo 'selected'; endif; ?>><?php echo e(__('insurance.status_cancelled')); ?></option>
                </select>
                <button class="btn btn-outline-primary text-nowrap" type="submit"><?php echo e(__('insurance.filter')); ?></button>
                <?php if(request()->filled('search') || request()->filled('status')): ?>
                    <a class="btn btn-outline-secondary" href="<?php echo e(route('insurance.index')); ?>" title="<?php echo e(__('insurance.clear_filter')); ?>"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
                <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#createInsuranceModal"><i class="bi bi-plus-lg me-1"></i><?php echo e(__('insurance.add_contract')); ?></button>
            </form>
        </div>

        <div class="card zedan-card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead><tr>
                            <th><?php echo e(__('insurance.company_name')); ?></th>
                            <th><?php echo e(__('insurance.contract_number')); ?></th>
                            <th><?php echo e(__('insurance.end_date')); ?></th>
                            <th><?php echo e(__('insurance.discount')); ?></th>
                            <th><?php echo e(__('insurance.status')); ?></th>
                            <th><?php echo e(__('insurance.recorded_by')); ?></th>
                            <th class="text-end"><?php echo e(__('insurance.actions')); ?></th>
                        </tr></thead>
                        <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $contracts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contract): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td data-label="<?php echo e(__('insurance.company_name')); ?>"><span class="fw-semibold"><?php echo e($contract->company_name); ?></span></td>
                                <td data-label="<?php echo e(__('insurance.contract_number')); ?>"><?php echo e($contract->contract_number); ?></td>
                                <td data-label="<?php echo e(__('insurance.end_date')); ?>"><?php echo e($contract->end_date->format('Y-m-d')); ?></td>
                                <td data-label="<?php echo e(__('insurance.discount')); ?>"><?php echo e($contract->discount_percentage); ?>%</td>
                                <td data-label="<?php echo e(__('insurance.status')); ?>"><span class="badge bg-<?php echo e($contract->status->badgeColor()); ?>"><?php echo e($contract->status->label()); ?></span></td>
                                <td data-label="<?php echo e(__('insurance.recorded_by')); ?>"><?php echo e($contract->creator?->name ?: '—'); ?></td>
                                <td data-label="<?php echo e(__('insurance.actions')); ?>" class="text-end"><div class="d-inline-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editInsuranceModal<?php echo e($contract->id); ?>" title="<?php echo e(__('insurance.edit')); ?>"><i class="bi bi-pencil"></i></button>
                                    <form action="<?php echo e(route('insurance.destroy', $contract)); ?>" method="POST" class="d-inline" onsubmit="return confirm('<?php echo e(__('insurance.confirm_delete')); ?>')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-outline-danger" title="<?php echo e(__('insurance.delete')); ?>"><i class="bi bi-trash"></i></button></form>
                                </div></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="7" class="text-center py-5"><div class="text-muted"><i class="bi bi-shield-check fs-2 d-block mb-2"></i><?php echo e(__('insurance.no_records')); ?></div></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if($contracts->hasPages()): ?><div class="card-footer bg-transparent border-0 pt-0 pb-3"><?php echo e($contracts->withQueryString()->links('pagination::bootstrap-5')); ?></div><?php endif; ?>
        </div>
    </div>

    <?php $__currentLoopData = $contracts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contract): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="modal fade" id="editInsuranceModal<?php echo e($contract->id); ?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
            <form action="<?php echo e(route('insurance.update', $contract)); ?>" method="POST"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="modal-header"><h5 class="modal-title"><?php echo e(__('insurance.edit_contract')); ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body"><?php echo $__env->make('insurance.partials.fields', ['contract' => $contract], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo e(__('insurance.cancel')); ?></button><button type="submit" class="btn btn-primary"><?php echo e(__('insurance.save')); ?></button></div>
            </form>
        </div></div></div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="modal fade" id="createInsuranceModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
        <form action="<?php echo e(route('insurance.store')); ?>" method="POST"><?php echo csrf_field(); ?>
            <div class="modal-header"><h5 class="modal-title"><?php echo e(__('insurance.add_contract')); ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body"><?php echo $__env->make('insurance.partials.fields', ['contract' => null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo e(__('insurance.cancel')); ?></button><button type="submit" class="btn btn-primary"><?php echo e(__('insurance.save')); ?></button></div>
        </form>
    </div></div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/insurance/index.blade.php ENDPATH**/ ?>