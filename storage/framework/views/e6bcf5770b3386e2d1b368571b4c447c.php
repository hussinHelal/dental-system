<?php $__env->startSection('title', __('messages.backups')); ?>

<?php $__env->startSection('content'); ?>
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 shadow-sm rounded-4 border border-body-secondary  p-3">
        <h3 class="mb-0"><?php echo e(__('messages.backups')); ?></h3>
        <div class="d-flex gap-2 flex-wrap">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', \App\Models\Backup::class)): ?>
                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importBackupModal">
                    <i class="bi bi-file-earmark-arrow-up"></i> <?php echo e(__('messages.import_backup')); ?>

                </button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#backupNowModal">
                    <i class="bi bi-cloud-arrow-up"></i> <?php echo e(__('messages.backup_now')); ?>

                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="card zedan-card shadow-sm">
        <div class="card-body p-0 shadow-sm">
            <?php if($backups->isEmpty()): ?>
                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
            <?php else: ?>
                <?php if($backups->contains('status', 'queued')): ?>
                    <div class="alert alert-info m-3 mb-0 py-2 small">
                        <i class="bi bi-arrow-repeat"></i> <?php echo e(__('messages.backup_queued_hint')); ?>

                    </div>
                <?php endif; ?>
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th><?php echo e(__('messages.date')); ?></th>
                                <th><?php echo e(__('messages.format')); ?></th>
                                <th><?php echo e(__('messages.status')); ?></th>
                                <th><?php echo e(__('messages.size')); ?></th>
                                <th><?php echo e(__('messages.generated_by')); ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $backups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $backup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td data-label="<?php echo e(__('messages.date')); ?>"><?php echo e($backup->generated_at->format('Y-m-d H:i')); ?></td>
                                    <td data-label="<?php echo e(__('messages.format')); ?>"><?php echo e(strtoupper($backup->type)); ?></td>
                                    <td data-label="<?php echo e(__('messages.status')); ?>">
                                        <span class="badge text-bg-<?php echo e($backup->statusBadgeClass()); ?>">
                                            <?php echo e(__('messages.backup_status_'.$backup->status)); ?>

                                        </span>
                                    </td>
                                    <td data-label="<?php echo e(__('messages.size')); ?>"><?php echo e($backup->isCompleted() ? $backup->humanSize() : '-'); ?></td>
                                    <td data-label="<?php echo e(__('messages.generated_by')); ?>"><?php echo e($backup->generator?->name ?? __('messages.automatic_monthly')); ?></td>
                                    <td data-label="">
                                        <?php if($backup->isCompleted()): ?>
                                            <a href="<?php echo e(route('backups.download', $backup)); ?>" class="btn btn-sm btn-outline-primary" title="<?php echo e(__('messages.download')); ?>">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            <?php if($backup->type === 'database'): ?>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#restoreInfoModal<?php echo e($backup->id); ?>" title="<?php echo e(__('messages.restore')); ?>">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $backup)): ?>
                                            <form data-ajax-form method="POST" action="<?php echo e(route('backups.destroy', $backup)); ?>" class="d-inline" data-confirm="<?php echo e(__('messages.confirm_delete')); ?>">
                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button class="btn btn-sm btn-outline-danger" title="<?php echo e(__('messages.delete')); ?>"><i class="bi bi-trash"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <?php if($backup->isCompleted() && $backup->type === 'database'): ?>
                                    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'restoreInfoModal'.$backup->id,'title' => __('messages.restore_instructions_title')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('restoreInfoModal'.$backup->id),'title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.restore_instructions_title'))]); ?>
                                        <p><?php echo e(__('messages.restore_instructions_intro')); ?></p>
                                        <ol class="small">
                                            <li><?php echo e(__('messages.restore_step_stop')); ?></li>
                                            <li>
                                                <?php echo e(__('messages.restore_step_run')); ?>

                                                <pre class="bg-body-secondary p-2 rounded mt-1 mb-1 small user-select-all">php artisan backup:restore <?php echo e($backup->id); ?></pre>
                                            </li>
                                            <li><?php echo e(__('messages.restore_step_restart')); ?></li>
                                        </ol>
                                        <p class="small text-secondary mb-0"><?php echo e(__('messages.restore_step_safety_note')); ?></p>
                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-3"><?php echo e($backups->links()); ?></div>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', \App\Models\Backup::class)): ?>
        <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'backupNowModal','title' => __('messages.backup_now')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'backupNowModal','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.backup_now'))]); ?>
            <form data-ajax-form method="POST" action="<?php echo e(route('backups.store')); ?>">
                <?php echo csrf_field(); ?>
                <?php if (isset($component)) { $__componentOriginal67ad07a4b593e690d435fee92e6413bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67ad07a4b593e690d435fee92e6413bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-select','data' => ['name' => 'type','label' => __('messages.format'),'required' => true,'options' => ['pdf' => 'PDF', 'excel' => 'Excel', 'both' => __('messages.both_zipped'), 'database' => __('messages.database_backup')]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'type','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.format')),'required' => true,'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['pdf' => 'PDF', 'excel' => 'Excel', 'both' => __('messages.both_zipped'), 'database' => __('messages.database_backup')])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal67ad07a4b593e690d435fee92e6413bb)): ?>
<?php $attributes = $__attributesOriginal67ad07a4b593e690d435fee92e6413bb; ?>
<?php unset($__attributesOriginal67ad07a4b593e690d435fee92e6413bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal67ad07a4b593e690d435fee92e6413bb)): ?>
<?php $component = $__componentOriginal67ad07a4b593e690d435fee92e6413bb; ?>
<?php unset($__componentOriginal67ad07a4b593e690d435fee92e6413bb); ?>
<?php endif; ?>
                <p class="small text-secondary"><?php echo e(__('messages.database_backup_hint')); ?></p>
                <button type="submit" class="btn btn-primary w-100"><?php echo e(__('messages.generate_backup')); ?></button>
            </form>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'importBackupModal','title' => __('messages.import_backup')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'importBackupModal','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.import_backup'))]); ?>
            <form data-ajax-form method="POST" action="<?php echo e(route('backups.import')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label"><?php echo e(__('messages.backup_file')); ?></label>
                    <input type="file" name="backup_file" class="form-control" accept=".sqlite,.zip" required>
                    <div class="form-text"><?php echo e(__('messages.import_backup_hint')); ?></div>
                </div>
                <button type="submit" class="btn btn-primary w-100"><?php echo e(__('messages.import_backup')); ?></button>
            </form>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/backups/index.blade.php ENDPATH**/ ?>