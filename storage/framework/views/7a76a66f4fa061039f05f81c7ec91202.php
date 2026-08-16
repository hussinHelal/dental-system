<?php $__env->startSection('title', __('finance.title')); ?>

<?php $__env->startSection('content'); ?>
    <script src="<?php echo e(asset('js/dental-ui.js')); ?>" defer></script>

    <div class="container-fluid px-0">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

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
                    <i class="bi bi-cash-coin text-primary"></i>
                </div>
                <h3 class="mb-0"><?php echo e(__('finance.title')); ?></h3>
            </div>

            <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#createTransactionModal">
                <i class="bi bi-plus-lg me-1"></i><?php echo e(__('finance.add_transaction')); ?>

            </button>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card zedan-card border-success shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small"><?php echo e(__('finance.total_income')); ?></div>
                        <div class="fs-4 fw-bold text-success"><?php echo e(number_format($totals['income'], 2)); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card zedan-card border-danger shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small"><?php echo e(__('finance.total_expense')); ?></div>
                        <div class="fs-4 fw-bold text-danger"><?php echo e(number_format($totals['expense'], 2)); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card zedan-card border-primary shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small"><?php echo e(__('finance.net_balance')); ?></div>
                        <div class="fs-4 fw-bold <?php echo e($totals['net'] >= 0 ? 'text-primary' : 'text-danger'); ?>">
                            <?php echo e(number_format($totals['net'], 2)); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" class="d-flex flex-wrap gap-2 mb-3">
            <select name="type" class="form-select" style="max-width: 160px;">
                <option value=""><?php echo e(__('finance.all')); ?></option>
                <option value="income" <?php if(request('type') === 'income'): echo 'selected'; endif; ?>><?php echo e(__('reports.income')); ?></option>
                <option value="expense" <?php if(request('type') === 'expense'): echo 'selected'; endif; ?>><?php echo e(__('reports.expense')); ?></option>
            </select>
            <input type="text" name="category" value="<?php echo e(request('category')); ?>" class="form-control" placeholder="<?php echo e(__('finance.category')); ?>" style="max-width: 200px;">
            <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="form-control" style="max-width: 170px;">
            <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="form-control" style="max-width: 170px;">
            <button class="btn btn-outline-primary text-nowrap" type="submit"><?php echo e(__('finance.filter')); ?></button>
            <?php if(request()->filled('type') || request()->filled('category') || request()->filled('from') || request()->filled('to')): ?>
                <a href="<?php echo e(route('finance.index')); ?>" class="btn btn-outline-secondary" title="<?php echo e(__('finance.filter')); ?>">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </form>

        <div class="card zedan-card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th><?php echo e(__('finance.date')); ?></th>
                                <th><?php echo e(__('finance.type')); ?></th>
                                <th><?php echo e(__('finance.category')); ?></th>
                                <th><?php echo e(__('finance.amount')); ?></th>
                                <th><?php echo e(__('finance.payment_method')); ?></th>
                                <th><?php echo e(__('finance.recorded_by')); ?></th>
                                <th class="text-end"><?php echo e(__('finance.actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td data-label="<?php echo e(__('finance.date')); ?>"><?php echo e($transaction->transaction_date->format('Y-m-d')); ?></td>
                                    <td data-label="<?php echo e(__('finance.type')); ?>">
                                        <span class="badge bg-<?php echo e($transaction->type->badgeColor()); ?>"><?php echo e($transaction->type->label()); ?></span>
                                    </td>
                                    <td data-label="<?php echo e(__('finance.category')); ?>"><?php echo e($transaction->category ?: '—'); ?></td>
                                    <td data-label="<?php echo e(__('finance.amount')); ?>">
                                        <span class="fw-semibold" data-compact-money data-value="<?php echo e($transaction->amount); ?>"><?php echo e(number_format($transaction->amount, 2)); ?></span>
                                    </td>
                                    <td data-label="<?php echo e(__('finance.payment_method')); ?>">
                                        <?php $knownMethods = ['cash', 'bank', 'other']; ?>
                                        <?php echo e($transaction->payment_method
                                            ? (in_array($transaction->payment_method, $knownMethods) ? __('finance.' . $transaction->payment_method) : $transaction->payment_method)
                                            : '—'); ?>

                                    </td>
                                    <td data-label="<?php echo e(__('finance.recorded_by')); ?>"><?php echo e($transaction->creator?->name ?: '—'); ?></td>
                                    <td data-label="<?php echo e(__('finance.actions')); ?>" class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTransactionModal<?php echo e($transaction->id); ?>" title="<?php echo e(__('finance.edit')); ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="<?php echo e(route('finance.destroy', $transaction)); ?>" method="POST" class="d-inline" onsubmit="return confirm('<?php echo e(__('finance.confirm_delete')); ?>')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button class="btn btn-sm btn-outline-danger" title="<?php echo e(__('finance.delete')); ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-cash-coin fs-2 d-block mb-2"></i>
                                            <?php echo e(__('finance.no_records')); ?>

                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if($transactions->hasPages()): ?>
                <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                    <?php echo e($transactions->withQueryString()->links('pagination::bootstrap-5')); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('finance.partials.edit-modal', ['transaction' => $transaction], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php echo $__env->make('finance.partials.create-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/finance/index.blade.php ENDPATH**/ ?>