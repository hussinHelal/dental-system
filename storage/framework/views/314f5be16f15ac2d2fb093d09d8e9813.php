<?php $__env->startSection('title', __('purchases.title')); ?>

<?php $__env->startSection('content'); ?>
    <script src="<?php echo e(asset('js/dental-ui.js')); ?>" defer></script>

    <style>
        tr.collapse.show, tr.collapsing { display: table-row; }
    </style>

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
                    <i class="bi bi-cart3 text-primary"></i>
                </div>
                <h3 class="mb-0"><?php echo e(__('purchases.title')); ?></h3>
            </div>

            <form method="GET" class="d-flex flex-wrap gap-2 flex-grow-1 justify-content-end">
                <select name="supplier_id" class="form-select" style="max-width: 220px;">
                    <option value=""><?php echo e(__('purchases.all')); ?></option>
                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($supplier->id); ?>" <?php if(request('supplier_id') == $supplier->id): echo 'selected'; endif; ?>><?php echo e($supplier->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <select name="payment_status" class="form-select" style="max-width: 180px;">
                    <option value=""><?php echo e(__('purchases.all')); ?></option>
                    <option value="paid" <?php if(request('payment_status') === 'paid'): echo 'selected'; endif; ?>><?php echo e(__('purchases.paid')); ?></option>
                    <option value="partial" <?php if(request('payment_status') === 'partial'): echo 'selected'; endif; ?>><?php echo e(__('purchases.partial')); ?></option>
                    <option value="unpaid" <?php if(request('payment_status') === 'unpaid'): echo 'selected'; endif; ?>><?php echo e(__('purchases.unpaid')); ?></option>
                </select>

                <button class="btn btn-outline-primary text-nowrap" type="submit"><?php echo e(__('purchases.filter')); ?></button>

                <?php if(request()->filled('supplier_id') || request()->filled('payment_status')): ?>
                    <a href="<?php echo e(route('purchases.index')); ?>" class="btn btn-outline-secondary" title="<?php echo e(__('purchases.filter')); ?>">
                        <i class="bi bi-x-lg"></i>
                    </a>
                <?php endif; ?>

                <a href="<?php echo e(route('suppliers.index')); ?>" class="btn btn-outline-primary text-nowrap">
                    <i class="bi bi-truck me-1"></i><?php echo e(__('suppliers.title')); ?>

                </a>

                <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#createPurchaseModal">
                    <i class="bi bi-plus-lg me-1"></i><?php echo e(__('purchases.add_purchase')); ?>

                </button>
            </form>
        </div>

        <div class="card zedan-card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th><?php echo e(__('purchases.date')); ?></th>
                                <th><?php echo e(__('purchases.supplier')); ?></th>
                                <th><?php echo e(__('purchases.items_count')); ?></th>
                                <th><?php echo e(__('purchases.total')); ?></th>
                                <th><?php echo e(__('purchases.payment_status')); ?></th>
                                <th><?php echo e(__('purchases.recorded_by')); ?></th>
                                <th class="text-end"><?php echo e(__('purchases.actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td data-label="<?php echo e(__('purchases.date')); ?>"><?php echo e($purchase->purchase_date->format('Y-m-d')); ?></td>
                                    <td data-label="<?php echo e(__('purchases.supplier')); ?>">
                                        <span class="fw-semibold"><?php echo e($purchase->supplier->name); ?></span>
                                    </td>
                                    <td data-label="<?php echo e(__('purchases.items_count')); ?>">
                                        <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none" data-bs-toggle="collapse" data-bs-target="#purchaseItems<?php echo e($purchase->id); ?>" aria-expanded="false">
                                            <?php echo e($purchase->items->count()); ?> <?php echo e(__('purchases.view_items')); ?>

                                        </button>
                                    </td>
                                    <td data-label="<?php echo e(__('purchases.total')); ?>">
                                        <span class="fw-semibold"><?php echo e(number_format($purchase->total_amount, 2)); ?></span>
                                    </td>
                                    <td data-label="<?php echo e(__('purchases.payment_status')); ?>">
                                        <span class="badge bg-<?php echo e($purchase->payment_status->badgeColor()); ?>"><?php echo e($purchase->payment_status->label()); ?></span>
                                    </td>
                                    <td data-label="<?php echo e(__('purchases.recorded_by')); ?>"><?php echo e($purchase->creator?->name ?: '—'); ?></td>
                                    <td data-label="<?php echo e(__('purchases.actions')); ?>" class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPurchaseModal<?php echo e($purchase->id); ?>" title="<?php echo e(__('purchases.edit')); ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="<?php echo e(route('purchases.destroy', $purchase)); ?>" method="POST" class="d-inline" onsubmit="return confirm('<?php echo e(__('purchases.confirm_delete')); ?>')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button class="btn btn-sm btn-outline-danger" title="<?php echo e(__('purchases.delete')); ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="collapse" id="purchaseItems<?php echo e($purchase->id); ?>">
                                    <td colspan="7" class="bg-body-tertiary">
                                        <div class="p-2 p-md-3">
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0 align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo e(__('purchases.item_name')); ?></th>
                                                            <th><?php echo e(__('purchases.quantity')); ?></th>
                                                            <th><?php echo e(__('purchases.unit_price')); ?></th>
                                                            <th><?php echo e(__('purchases.subtotal')); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__currentLoopData = $purchase->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <tr>
                                                                <td><?php echo e($item->item_name); ?></td>
                                                                <td><?php echo e($item->quantity); ?></td>
                                                                <td><?php echo e(number_format($item->unit_price, 2)); ?></td>
                                                                <td><?php echo e(number_format($item->subtotal, 2)); ?></td>
                                                            </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-cart3 fs-2 d-block mb-2"></i>
                                            <?php echo e(__('purchases.no_records')); ?>

                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if($purchases->hasPages()): ?>
                <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                    <?php echo e($purchases->withQueryString()->links('pagination::bootstrap-5')); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('purchases.partials.edit-modal', ['purchase' => $purchase], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php echo $__env->make('purchases.partials.create-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/purchases/index.blade.php ENDPATH**/ ?>