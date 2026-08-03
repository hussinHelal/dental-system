<?php $__env->startSection('title', __('messages.activity_log')); ?>

<?php $__env->startSection('content'); ?>
    <h3 class="mb-3"><?php echo e(__('messages.activity_log')); ?></h3>

    <div class="card zedan-card mb-3 shadow-sm">
        <div class="card-body shadow-sm">
            <form method="GET" class="row g-2">
                <div class="col-md-3">
                    <input type="search" name="q" class="form-control" placeholder="<?php echo e(__('messages.search_placeholder')); ?>" value="<?php echo e(request('q')); ?>">
                </div>
                <div class="col-md-3">
                    <select name="causer_id" class="form-select">
                        <option value=""><?php echo e(__('messages.all_staff')); ?></option>
                        <?php $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($person->id); ?>" <?php if(request('causer_id') == $person->id): echo 'selected'; endif; ?>><?php echo e($person->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="log_name" class="form-select">
                        <option value=""><?php echo e(__('messages.all_modules')); ?></option>
                        <?php $__currentLoopData = $logNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $logName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($logName); ?>" <?php if(request('log_name') === $logName): echo 'selected'; endif; ?>><?php echo e(__('messages.module_'.$logName)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="event" class="form-select">
                        <option value=""><?php echo e(__('messages.all_actions')); ?></option>
                        <?php $__currentLoopData = ['created', 'updated', 'deleted', 'login', 'logout', 'failed_login', 'blocked_login_deactivated']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eventOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($eventOption); ?>" <?php if(request('event') === $eventOption): echo 'selected'; endif; ?>><?php echo e(__('messages.event_'.$eventOption)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-0"><?php echo e(__('messages.date_from')); ?></label>
                    <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-0"><?php echo e(__('messages.date_to')); ?></label>
                    <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> <?php echo e(__('messages.search_placeholder')); ?></button>
                </div>
            </form>
        </div>
    </div>

    <div class="card zedan-card shadow-sm">
        <div class="card-body p-0 shadow-sm">
            <?php if($activities->isEmpty()): ?>
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
                                <th><?php echo e(__('messages.who')); ?></th>
                                <th><?php echo e(__('messages.action')); ?></th>
                                <th><?php echo e(__('messages.module')); ?></th>
                                <th><?php echo e(__('messages.changes')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td data-label="<?php echo e(__('messages.date')); ?>"><?php echo e($activity->created_at->format('Y-m-d H:i')); ?></td>
                                    <td data-label="<?php echo e(__('messages.who')); ?>"><?php echo e($activity->causer?->name ?? __('messages.unknown_user')); ?></td>
                                    <td data-label="<?php echo e(__('messages.action')); ?>">
                                        <span class="badge text-bg-<?php echo e(in_array($activity->event, ['deleted', 'failed_login', 'blocked_login_deactivated']) ? 'danger' : ($activity->event === 'created' ? 'success' : 'secondary')); ?>">
                                            <?php echo e(__('messages.event_'.$activity->event)); ?>

                                        </span>
                                    </td>
                                    <td data-label="<?php echo e(__('messages.module')); ?>"><?php echo e($activity->log_name ? __('messages.module_'.$activity->log_name) : '-'); ?></td>
                                    <td data-label="<?php echo e(__('messages.changes')); ?>">
                                        <?php
                                            $attributes = data_get($activity->properties, 'attributes', []);
                                            $old = data_get($activity->properties, 'old', []);
                                        ?>
                                        <?php if(count($attributes)): ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#activityDetail<?php echo e($activity->id); ?>">
                                                <?php echo e(__('messages.view')); ?>

                                            </button>
                                            <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'activityDetail'.$activity->id,'title' => __('messages.changes')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('activityDetail'.$activity->id),'title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.changes'))]); ?>
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo e(__('messages.field')); ?></th>
                                                            <th><?php echo e(__('messages.old_value')); ?></th>
                                                            <th><?php echo e(__('messages.new_value')); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__currentLoopData = $attributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $newValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <tr>
                                                                <td><?php echo e($field); ?></td>
                                                                <td class="text-secondary"><?php echo e(data_get($old, $field, '-')); ?></td>
                                                                <td><?php echo e($newValue); ?></td>
                                                            </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
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
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-3 "><?php echo e($activities->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/activity-log/index.blade.php ENDPATH**/ ?>