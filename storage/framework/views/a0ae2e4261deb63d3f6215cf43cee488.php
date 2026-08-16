<?php $__env->startSection('title', __('dental_labs.labs_title')); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-0">
        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <ul class="mb-0 ps-3">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3 shadow-sm p-3 rounded-4 zedan-page-header">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary-subtle p-2">
                    <i class="bi bi-building-gear text-primary"></i>
                </div>
                <div>
                    <h3 class="mb-0"><?php echo e(__('dental_labs.labs_title')); ?></h3>
                </div>
            </div>

            <form method="GET" class="d-flex flex-wrap gap-2 flex-grow-1 justify-content-end" role="search">
                <div class="input-group" style="max-width: 320px;">
                    <span class="input-group-text bg-body"><i class="bi bi-search"></i></span>
                    <input id="labSearch" type="search" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="<?php echo e(__('dental_labs.search')); ?>" maxlength="100" autocomplete="off">
                </div>

                <button class="btn btn-outline-primary text-nowrap" type="submit"><?php echo e(__('dental_labs.filter')); ?></button>

                <?php if(request()->filled('search')): ?>
                    <a class="btn btn-outline-secondary" href="<?php echo e(route('dental-labs.index')); ?>">
                        <i class="bi bi-x-lg"></i>
                    </a>
                <?php endif; ?>

                <a href="<?php echo e(route('lab-cases.index')); ?>" class="btn btn-outline-primary text-nowrap">
                    <i class="bi bi-box-seam me-1"></i><?php echo e(__('dental_labs.view_cases')); ?>

                </a>

                <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#createLabModal">
                    <i class="bi bi-plus-lg me-1"></i><?php echo e(__('dental_labs.add_lab')); ?>

                </button>
            </form>
        </div>

        <div class="card zedan-card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th><?php echo e(__('dental_labs.name')); ?></th>
                                <th><?php echo e(__('dental_labs.phone')); ?></th>
                                <th><?php echo e(__('dental_labs.address')); ?></th>
                                <th><?php echo e(__('dental_labs.cases_count')); ?></th>
                                <th class="text-end"><?php echo e(__('dental_labs.actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $labs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td data-label="<?php echo e(__('dental_labs.name')); ?>">
                                        <span class="fw-semibold"><?php echo e($lab->name); ?></span>
                                    </td>
                                    <td data-label="<?php echo e(__('dental_labs.phone')); ?>"><?php echo e($lab->phone ?: '—'); ?></td>
                                    <td data-label="<?php echo e(__('dental_labs.address')); ?>"><?php echo e($lab->address ?: '—'); ?></td>
                                    <td data-label="<?php echo e(__('dental_labs.cases_count')); ?>">
                                        <span class="badge text-bg-light border"><?php echo e($lab->cases_count); ?></span>
                                    </td>
                                    <td data-label="<?php echo e(__('dental_labs.actions')); ?>" class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editLabModal<?php echo e($lab->id); ?>" title="<?php echo e(__('dental_labs.edit')); ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="<?php echo e(route('dental-labs.destroy', $lab)); ?>" method="POST" class="d-inline" onsubmit="return confirm('<?php echo e(__('dental_labs.confirm_delete')); ?>')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button class="btn btn-sm btn-outline-danger" title="<?php echo e(__('dental_labs.delete')); ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-building fs-2 d-block mb-2"></i>
                                            <?php echo e(__('dental_labs.no_labs')); ?>

                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if($labs->hasPages()): ?>
                <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                    <?php echo e($labs->withQueryString()->links('pagination::bootstrap-5')); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php $__currentLoopData = $labs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('dental-labs.partials.edit-modal', ['lab' => $lab], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php echo $__env->make('dental-labs.partials.create-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/dental-labs/index.blade.php ENDPATH**/ ?>