<?php $__env->startSection('title', __('messages.dashboard')); ?>

<?php $__env->startSection('content'); ?>
    <h3 class="mb-4"><?php echo e(__('messages.dashboard')); ?></h3>

    <div class="row g-3 mb-4 shadow-sm">
        <div class="col-6 col-md-3">
            <div class="card zedan-stat-card h-100">
                <div class="card-body">
                    <div class="text-secondary small"><?php echo e(__('messages.todays_appointments')); ?></div>
                    <div class="fs-3 fw-bold"><?php echo e($todaysAppointments->count()); ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card zedan-stat-card h-100">
                <div class="card-body">
                    <div class="text-secondary small"><?php echo e(__('messages.low_stock_items')); ?></div>
                    <div class="fs-3 fw-bold <?php echo e($lowStockItems->count() ? 'text-danger' : ''); ?>"><?php echo e($lowStockItems->count()); ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card zedan-stat-card h-100">
                <div class="card-body">
                    <div class="text-secondary small"><?php echo e(__('messages.active_doctors')); ?></div>
                    <div class="fs-3 fw-bold"><?php echo e($activeDoctorsCount); ?></div>
                </div>
            </div>
        </div>
        <?php if($financials): ?>
            <div class="col-6 col-md-3 shadow-sm">
                <div class="card zedan-stat-card h-100">
                    <div class="card-body">
                        <div class="text-secondary small"><?php echo e(__('messages.todays_revenue')); ?></div>
                        <div class="fs-3 fw-bold text-success"><?php echo e(number_format($financials['todays_revenue'], 2)); ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if($financials): ?>
        <div class="row g-3 mb-4 shadow-sm">
            <div class="col-md-4">
                <div class="card zedan-card">
                    <div class="card-body">
                        <div class="text-secondary small"><?php echo e(__('messages.pending_payments')); ?></div>
                        <div class="fs-4 fw-bold text-danger"><?php echo e(number_format($financials['pending_payments'], 2)); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card zedan-card">
                    <div class="card-body">
                        <div class="text-secondary small"><?php echo e(__('messages.installment_totals')); ?></div>
                        <div class="fs-4 fw-bold text-warning"><?php echo e(number_format($financials['installment_totals'], 2)); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card zedan-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-secondary small"><?php echo e(__('messages.last_backup')); ?></div>
                            <div class="fw-semibold">
                                <?php echo e($lastBackup?->generated_at?->format('Y-m-d H:i') ?? __('messages.never')); ?>

                            </div>
                        </div>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', \App\Models\Backup::class)): ?>
                            <a href="<?php echo e(route('backups.index')); ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-cloud-arrow-up"></i> <?php echo e(__('messages.backup_now')); ?>

                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card zedan-card mb-4 shadow-sm">
            <div class="card-body">
                <h6 class="mb-3"><?php echo e(__('messages.weekly_revenue')); ?></h6>
                <canvas id="weeklyRevenueChart" height="90" data-points='<?php echo json_encode($weeklyRevenue, 15, 512) ?>'></canvas>
            </div>
        </div>
    <?php endif; ?>

    <div class="card zedan-card shadow-sm">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <span><?php echo e(__('messages.todays_appointments')); ?></span>
            <a href="<?php echo e(route('appointments.index')); ?>" class="btn btn-sm btn-outline-primary"><?php echo e(__('messages.view_schedule')); ?></a>
        </div>
        <div class="card-body p-0">
            <?php if($todaysAppointments->isEmpty()): ?>
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
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0">
                        <thead>
                            <tr>
                                <th><?php echo e(__('messages.time')); ?></th>
                                <th><?php echo e(__('messages.patient')); ?></th>
                                <th><?php echo e(__('messages.doctor')); ?></th>
                                <th><?php echo e(__('messages.room')); ?></th>
                                <th><?php echo e(__('messages.status')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $todaysAppointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td data-label="<?php echo e(__('messages.time')); ?>"><?php echo e($appointment->start_time); ?> - <?php echo e($appointment->end_time); ?></td>
                                    <td data-label="<?php echo e(__('messages.patient')); ?>"><?php echo e($appointment->patient->full_name); ?></td>
                                    <td data-label="<?php echo e(__('messages.doctor')); ?>"><?php echo e($appointment->doctor->name); ?></td>
                                    <td data-label="<?php echo e(__('messages.room')); ?>"><?php echo e($appointment->room->name); ?></td>
                                    <td data-label="<?php echo e(__('messages.status')); ?>">
                                        <span class="badge text-bg-secondary"><?php echo e(__('messages.status_'.$appointment->status)); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/dashboard/index.blade.php ENDPATH**/ ?>