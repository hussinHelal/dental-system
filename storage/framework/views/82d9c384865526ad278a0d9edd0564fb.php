<?php $__env->startSection('title', __('suppliers.title')); ?>

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
                    <i class="bi bi-truck text-primary"></i>
                </div>
                <div>
                    <h3 class="mb-0"><?php echo e(__('suppliers.title')); ?></h3>
                    
                </div>
            </div>

            <form method="GET" class="d-flex flex-wrap gap-2 flex-grow-1 justify-content-end" role="search">
                <div class="input-group" style="max-width: 320px;">
                    <span class="input-group-text bg-body"><i class="bi bi-search"></i></span>
                    <input type="search" name="search" value="<?php echo e(request('search')); ?>"
                           class="form-control" placeholder="<?php echo e(__('suppliers.search')); ?>"
                           maxlength="100" autocomplete="off">
                </div>
                <button class="btn btn-outline-primary text-nowrap" type="submit">
                    <?php echo e(__('suppliers.filter')); ?>

                </button>
                <?php if(request()->filled('search')): ?>
                    <a href="<?php echo e(route('suppliers.index')); ?>" class="btn btn-outline-secondary text-nowrap">
                        <i class="bi bi-x-lg"></i>
                    </a>
                <?php endif; ?>
                <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#createSupplierModal">
                    <i class="bi bi-plus-lg me-1"></i><?php echo e(__('suppliers.add_supplier')); ?>

                </button>
            </form>
        </div>

        <div class="card zedan-card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th><?php echo e(__('suppliers.name')); ?></th>
                                <th><?php echo e(__('suppliers.phone')); ?></th>
                                <th><?php echo e(__('suppliers.address')); ?></th>
                                <th><?php echo e(__('suppliers.purchases_count')); ?></th>
                                <th class="text-end"><?php echo e(__('suppliers.actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td data-label="<?php echo e(__('suppliers.name')); ?>">
                                        <div class="fw-semibold"><?php echo e($supplier->name); ?></div>
                                    </td>
                                    <td data-label="<?php echo e(__('suppliers.phone')); ?>"><?php echo e($supplier->phone ?: '—'); ?></td>
                                    <td data-label="<?php echo e(__('suppliers.address')); ?>"><?php echo e($supplier->address ?: '—'); ?></td>
                                    <td data-label="<?php echo e(__('suppliers.purchases_count')); ?>">
                                        <span class="badge text-bg-light border"><?php echo e($supplier->purchases_count); ?></span>
                                    </td>
                                    <td data-label="<?php echo e(__('suppliers.actions')); ?>" class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal" data-bs-target="#editSupplierModal<?php echo e($supplier->id); ?>"
                                                    title="<?php echo e(__('suppliers.edit')); ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="<?php echo e(route('suppliers.destroy', $supplier)); ?>" method="POST" class="d-inline"
                                                  onsubmit="return confirm('<?php echo e(__('suppliers.confirm_delete')); ?>')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button class="btn btn-sm btn-outline-danger" title="<?php echo e(__('suppliers.delete')); ?>">
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
                                            <i class="bi bi-truck fs-2 d-block mb-2"></i>
                                            <?php echo e(__('suppliers.no_records')); ?>

                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if($suppliers->hasPages()): ?>
                <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                    <?php echo e($suppliers->withQueryString()->links('pagination::bootstrap-5')); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="modal fade" id="createSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="<?php echo e(route('suppliers.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo e(__('suppliers.add_supplier')); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body"><?php echo $__env->make('suppliers.partials.fields', ['supplier' => null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo e(__('suppliers.cancel')); ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo e(__('suppliers.save')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/suppliers/index.blade.php ENDPATH**/ ?>