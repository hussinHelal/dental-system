<?php $__env->startSection('title', __('messages.advanced_search')); ?>

<?php $__env->startSection('content'); ?>
    <h3 class="mb-3"><?php echo e(__('messages.advanced_search')); ?></h3>

    <div class="card zedan-card mb-3 shadow-sm">
        <div class="card-body shadow-sm">
            <form method="GET" action="<?php echo e(route('appointments.search')); ?>" class="row g-2">
                <div class="col-md-3">
                    <input type="search" name="q" class="form-control" placeholder="<?php echo e(__('messages.search_placeholder')); ?>" value="<?php echo e(request('q')); ?>">
                </div>
                <div class="col-md-2">
                    <select name="doctor_id" class="form-select">
                        <option value=""><?php echo e(__('messages.all_doctors')); ?></option>
                        <?php $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($doctor->id); ?>" <?php if(request('doctor_id') == $doctor->id): echo 'selected'; endif; ?>><?php echo e($doctor->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="room_id" class="form-select">
                        <option value=""><?php echo e(__('messages.all_rooms')); ?></option>
                        <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($room->id); ?>" <?php if(request('room_id') == $room->id): echo 'selected'; endif; ?>><?php echo e($room->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="visit_type" class="form-select">
                        <option value=""><?php echo e(__('messages.visit_type')); ?></option>
                        <option value="initial_consultation" <?php if(request('visit_type') === 'initial_consultation'): echo 'selected'; endif; ?>><?php echo e(__('messages.visit_type_initial_consultation')); ?></option>
                        <option value="follow_up" <?php if(request('visit_type') === 'follow_up'): echo 'selected'; endif; ?>><?php echo e(__('messages.visit_type_follow_up')); ?></option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value=""><?php echo e(__('messages.status')); ?></option>
                        <?php $__currentLoopData = ['scheduled','in_progress','completed','cancelled','no_show']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($status); ?>" <?php if(request('status') === $status): echo 'selected'; endif; ?>><?php echo e(__('messages.status_'.$status)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-0"><?php echo e(__('messages.date_from')); ?></label>
                    <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-0"><?php echo e(__('messages.date_to')); ?></label>
                    <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
                </div>
            </form>
        </div>
    </div>

    <div class="card zedan-card shadow-sm">
        <div class="card-body p-0 shadow-sm">
            <?php if($appointments->isEmpty()): ?>
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
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th><?php echo e(__('messages.date')); ?></th>
                                <th><?php echo e(__('messages.patient')); ?></th>
                                <th><?php echo e(__('messages.doctor')); ?></th>
                                <th><?php echo e(__('messages.room')); ?></th>
                                <th><?php echo e(__('messages.status')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td data-label="<?php echo e(__('messages.date')); ?>"><?php echo e($appointment->appointment_date->toDateString()); ?> <?php echo e($appointment->time_range_formatted); ?></td>
                                    <td data-label="<?php echo e(__('messages.patient')); ?>"><?php echo e($appointment->patient->full_name); ?></td>
                                    <td data-label="<?php echo e(__('messages.doctor')); ?>"><?php echo e($appointment->doctor->name); ?></td>
                                    <td data-label="<?php echo e(__('messages.room')); ?>"><?php echo e($appointment->room->name); ?></td>
                                    <td data-label="<?php echo e(__('messages.status')); ?>"><span class="badge text-bg-secondary"><?php echo e(__('messages.status_'.$appointment->status)); ?></span></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-3"><?php echo e($appointments->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/appointments/search.blade.php ENDPATH**/ ?>