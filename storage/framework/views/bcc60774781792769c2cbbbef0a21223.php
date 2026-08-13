<?php $__env->startSection('title', __('assets.title')); ?>

<?php $__env->startSection('content'); ?>
    <script src="<?php echo e(asset('js/dental-ui.js')); ?>" defer></script>
    <div class="container-fluid px-0">
        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <ul class="mb-0 ps-3"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3 shadow-sm p-3 rounded-4 zedan-page-header">
            <div class="d-flex align-items-center gap-2"><div class="rounded-circle bg-primary-subtle p-2"><i class="bi bi-pc-display text-primary"></i></div><div><h3 class="mb-0"><?php echo e(__('assets.title')); ?></h3></div></div>
            <form method="GET" class="d-flex flex-wrap gap-2 flex-grow-1 justify-content-end" role="search">
                <div class="input-group" style="max-width: 320px;"><span class="input-group-text bg-body"><i class="bi bi-search"></i></span><input id="assetSearch" type="search" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="<?php echo e(__('assets.search')); ?>" maxlength="100" autocomplete="off"></div>
                <button class="btn btn-outline-primary text-nowrap" type="submit"><?php echo e(__('assets.filter')); ?></button>
                <?php if(request()->filled('search')): ?><a class="btn btn-outline-secondary" href="<?php echo e(route('assets.index')); ?>"><i class="bi bi-x-lg"></i></a><?php endif; ?>
                <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#createAssetModal"><i class="bi bi-plus-lg me-1"></i><?php echo e(__('assets.add_asset')); ?></button>
            </form>
        </div>

        <div class="card zedan-card shadow-sm"><div class="card-body p-0"><div class="table-responsive">
            <table class="table zedan-responsive-table mb-0 align-middle"><thead><tr>
                <th><?php echo e(__('assets.name')); ?></th><th><?php echo e(__('assets.category')); ?></th><th><?php echo e(__('assets.purchase_date')); ?></th><th><?php echo e(__('assets.purchase_cost')); ?></th><th><?php echo e(__('assets.book_value')); ?></th><th><?php echo e(__('assets.recorded_by')); ?></th><th class="text-end"><?php echo e(__('assets.actions')); ?></th>
            </tr></thead><tbody>
            <?php $__empty_1 = true; $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td data-label="<?php echo e(__('assets.name')); ?>"><span class="fw-semibold"><?php echo e($asset->name); ?></span></td>
                    <td data-label="<?php echo e(__('assets.category')); ?>"><?php echo e($asset->category); ?></td>
                    <td data-label="<?php echo e(__('assets.purchase_date')); ?>"><?php echo e($asset->purchase_date->format('Y-m-d')); ?></td>
                    <td data-label="<?php echo e(__('assets.purchase_cost')); ?>"><span data-compact-money data-value="<?php echo e($asset->purchase_cost); ?>"><?php echo e(number_format($asset->purchase_cost, 2)); ?></span></td>
                    <td data-label="<?php echo e(__('assets.book_value')); ?>"><span class="fw-semibold"><span data-compact-money data-value="<?php echo e($asset->bookValue()); ?>"><?php echo e(number_format($asset->bookValue(), 2)); ?></span></span></td>
                    <td data-label="<?php echo e(__('assets.recorded_by')); ?>"><?php echo e($asset->creator?->name ?: '—'); ?></td>
                    <td data-label="<?php echo e(__('assets.actions')); ?>" class="text-end"><div class="d-inline-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editAssetModal<?php echo e($asset->id); ?>" title="<?php echo e(__('assets.edit')); ?>"><i class="bi bi-pencil"></i></button>
                        <form action="<?php echo e(route('assets.destroy', $asset)); ?>" method="POST" class="d-inline" onsubmit="return confirm('<?php echo e(__('assets.confirm_delete')); ?>')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-outline-danger" title="<?php echo e(__('assets.delete')); ?>"><i class="bi bi-trash"></i></button></form>
                    </div></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="text-center py-5"><div class="text-muted"><i class="bi bi-pc-display fs-2 d-block mb-2"></i><?php echo e(__('assets.no_records')); ?></div></td></tr>
            <?php endif; ?>
            </tbody></table>
        </div></div><?php if($assets->hasPages()): ?><div class="card-footer bg-transparent border-0 pt-0 pb-3"><?php echo e($assets->withQueryString()->links('pagination::bootstrap-5')); ?></div><?php endif; ?></div>
    </div>

    <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="modal fade" id="editAssetModal<?php echo e($asset->id); ?>" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
            <form action="<?php echo e(route('assets.update', $asset)); ?>" method="POST" enctype="multipart/form-data"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="modal-header"><h5 class="modal-title"><?php echo e(__('assets.edit_asset')); ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body"><?php echo $__env->make('assets.partials.fields', ['asset' => $asset], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo e(__('assets.cancel')); ?></button><button type="submit" class="btn btn-primary"><?php echo e(__('assets.save')); ?></button></div>
            </form>
        </div></div></div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="modal fade" id="createAssetModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
        <form action="<?php echo e(route('assets.store')); ?>" method="POST" enctype="multipart/form-data"><?php echo csrf_field(); ?>
            <div class="modal-header"><h5 class="modal-title"><?php echo e(__('assets.add_asset')); ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body"><?php echo $__env->make('assets.partials.fields', ['asset' => null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo e(__('assets.cancel')); ?></button><button type="submit" class="btn btn-primary"><?php echo e(__('assets.save')); ?></button></div>
        </form>
    </div></div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/assets/index.blade.php ENDPATH**/ ?>