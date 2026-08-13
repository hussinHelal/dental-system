<?php $__env->startSection('title', $patient->full_name); ?>

<?php $__env->startSection('content'); ?>
<?php
$toothStatusColors = [
    'healthy' => 'success',
    'decayed' => 'danger',
    'filled' => 'primary',
    'crown' => 'warning',
    'root_canal' => 'info',
    'extracted' => 'secondary',
    'missing' => 'dark',
    'implant' => 'success',
    'fractured' => 'danger',
    'abscess' => 'danger',
    'braces' => 'primary',
    'veneer' => 'warning',
    'wisdom' => 'secondary',
];
?>
    <a href="<?php echo e(route('patients.index')); ?>" class="btn btn-sm btn-primary mb-2 shadow-sm">
        <i class="bi bi-arrow-<?php echo e(app()->getLocale() === 'ar' ? 'right' : 'left'); ?> me-1"></i>
        <?php echo e(__('messages.back')); ?>

    </a>

    
    <div class="card zedan-card mb-4 shadow-sm">
        <div class="card-body shadow-sm">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div class="d-flex gap-3 align-items-center">
                    <img src="<?php echo e($patient->photoUrl()); ?>" width="64" height="64" class="rounded-circle" alt="<?php echo e($patient->full_name); ?>" data-image-preview style="cursor: pointer;">
                    <div>
                        <h4 class="mb-1"><?php echo e($patient->full_name); ?></h4>
                        <div class="text-secondary">
                            <?php echo e($patient->phone); ?>

                            <?php if($patient->display_age): ?> &middot; <?php echo e(__('messages.age')); ?>: <?php echo e($patient->display_age); ?> <?php endif; ?>
                            <?php if($patient->gender): ?> &middot; <?php echo e(__('messages.gender_'.$patient->gender)); ?> <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="<?php echo e(route('appointments.index', ['date' => now()->toDateString() , 'book_for' => $patient->id ])); ?>" class="btn btn-sm btn-primary">
                        <i class="bi bi-calendar-plus"></i> <?php echo e(__('messages.book_follow_up')); ?>

                    </a>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $patient)): ?>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPatientModal">
                            <i class="bi bi-pencil"></i> <?php echo e(__('messages.edit')); ?>

                        </button>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $patient)): ?>
                        <form data-ajax-form method="POST" action="<?php echo e(route('patients.destroy', $patient)); ?>" data-confirm="<?php echo e(__('messages.confirm_delete')); ?>">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($patient->address || $patient->notes): ?>
                <hr>
                <div class="row g-3">
                    <?php if($patient->address): ?>
                        <div class="col-md-6">
                            <div class="text-secondary small"><?php echo e(__('messages.address')); ?></div>
                            <div><?php echo e($patient->address); ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if($patient->notes): ?>
                        <div class="col-md-6">
                            <div class="text-secondary small"><?php echo e(__('messages.notes')); ?></div>
                            <div><?php echo e($patient->notes); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="card zedan-card mb-4">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <span><i class="bi bi-credit-card me-2"></i><?php echo e(__('messages.payments')); ?></span>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', \App\Models\Payment::class)): ?>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                    <i class="bi bi-plus-lg"></i> <?php echo e(__('messages.record_payment')); ?>

                </button>
            <?php endif; ?>
        </div>
        <div class="card-body shadow-sm">
            <div class="row text-center mb-3 g-2">
                <div class="col-4">
                    <div class="text-secondary small"><?php echo e(__('messages.total_cost')); ?></div>
                    <div class="fw-bold"><?php echo e(number_format($summary['total_cost'], 2)); ?></div>
                </div>
                <div class="col-4">
                    <div class="text-secondary small"><?php echo e(__('messages.paid')); ?></div>
                    <div class="fw-bold text-success"><?php echo e(number_format($summary['paid'], 2)); ?></div>
                </div>
                <div class="col-4">
                    <div class="text-secondary small"><?php echo e(__('messages.remaining')); ?></div>
                    <div class="fw-bold text-danger"><?php echo e(number_format($summary['remaining'], 2)); ?></div>
                </div>
            </div>

            <?php if($payments->isEmpty()): ?>
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
                <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border rounded p-2 mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold"><?php echo e($payment->treatment->name); ?></div>
                                <div class="small text-secondary">
                                    <?php echo e(__('messages.total')); ?>: <?php echo e(number_format($payment->total_amount, 2)); ?>

                                    &middot; <?php echo e(__('messages.remaining')); ?>: <?php echo e(number_format($payment->remaining_balance, 2)); ?>

                                </div>
                            </div>
                            <span class="badge text-bg-<?php echo e($payment->statusBadgeClass()); ?>">
                                <?php echo e(__('messages.payment_status_'.$payment->status)); ?>

                            </span>
                        </div>

                        <?php if($payment->payment_type === 'installment'): ?>
                            <div class="mt-2">
                                <?php $__currentLoopData = $payment->installments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $installment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="small text-secondary">
                                        <?php echo e($installment->paid_date->toDateString()); ?> - <?php echo e(number_format($installment->amount, 2)); ?>

                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $payment)): ?>
                                    <?php if($payment->remaining_balance > 0): ?>
                                        <button class="btn btn-sm btn-outline-primary mt-1" data-bs-toggle="modal" data-bs-target="#addInstallmentModal<?php echo e($payment->id); ?>">
                                            <?php echo e(__('messages.add_installment')); ?>

                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $payment)): ?>
                            <form data-ajax-form method="POST" action="<?php echo e(route('payments.destroy', $payment)); ?>" class="mt-2" data-confirm="<?php echo e(__('messages.confirm_delete')); ?>">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> <?php echo e(__('messages.delete')); ?></button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <?php if($payment->payment_type === 'installment'): ?>
                        <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'addInstallmentModal'.$payment->id,'title' => __('messages.add_installment')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('addInstallmentModal'.$payment->id),'title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.add_installment'))]); ?>
                            <form data-ajax-form method="POST" action="<?php echo e(route('payments.installments.store', $payment)); ?>">
                                <?php echo csrf_field(); ?>
                                <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['type' => 'number','step' => '0.01','name' => 'amount','label' => __('messages.amount'),'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'number','step' => '0.01','name' => 'amount','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.amount')),'required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $attributes = $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $component = $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['type' => 'date','name' => 'paid_date','label' => __('messages.date'),'value' => now()->toDateString(),'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'date','name' => 'paid_date','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.date')),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(now()->toDateString()),'required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $attributes = $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $component = $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
                                <button type="submit" class="btn btn-primary w-100"><?php echo e(__('messages.save')); ?></button>
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
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div class="mt-2"><?php echo e($payments->links()); ?></div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="card shadow-sm mb-4">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-grid-3x3-gap"></i> <?php echo e(__('messages.tooth_chart')); ?></h5>
        <a href="<?php echo e(route('patients.tooth-chart', $patient)); ?>" class="btn btn-sm btn-primary">
            <?php echo e(__('messages.open_chart')); ?>

        </a>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-1 justify-content-center">
            <?php $toothMap = $patient->toothRecords->keyBy('tooth_number'); ?>
            <?php $__currentLoopData = range(1, 32); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $record = $toothMap[$num] ?? null; ?>
                <span class="badge bg-<?php echo e([
                    'healthy' => 'success', 'decayed' => 'danger', 'filled' => 'primary',
                    'crown' => 'warning', 'root_canal' => 'info', 'extracted' => 'secondary',
                    'missing' => 'dark', 'implant' => 'success', 'fractured' => 'danger',
                    'abscess' => 'danger', 'braces' => 'primary', 'veneer' => 'warning'
                ][$record?->status ?? 'healthy']); ?>">
                    <?php echo e($num); ?>

                </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>


<div class="card zedan-card mb-4">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <span><i class="bi bi-x-ray me-2"></i><?php echo e(__('messages.xray_photo')); ?></span>
        <?php if($patient->xray_photo): ?>
            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#xrayViewModal">
                <i class="bi bi-eye"></i> <?php echo e(__('messages.view')); ?>

            </button>
        <?php endif; ?>
    </div>
    <div class="card-body text-center">
        <?php if($patient->xray_photo): ?>
            <img src="<?php echo e($patient->xrayPhotoUrl()); ?>" class="img-fluid rounded shadow-sm xray-image mb-2" alt="<?php echo e(__('messages.xray_photo')); ?>" style="max-height: 250px; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#xrayViewModal">
        <?php else: ?>
            <p class="text-muted mb-0"><i class="bi bi-image fs-1"></i></p>
            <p class="text-muted small mb-0"><?php echo e(__('messages.no_xray') ?? 'No X-ray uploaded.'); ?></p>
        <?php endif; ?>
    </div>
</div>


<div class="card zedan-card mb-4">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <span><i class="bi bi-palette me-2"></i><?php echo e(__('messages.crown_color')); ?></span>
        <?php if($patient->crown_color): ?>
            <span class="badge bg-primary fs-6"><?php echo e($patient->crown_color); ?></span>
        <?php endif; ?>
    </div>
    <div class="card-body text-center">
        <?php if($patient->crown_color): ?>
            <div class="py-2">
                <span class="display-5 fw-bold text-primary font-monospace"><?php echo e($patient->crown_color); ?></span>
            </div>
        <?php else: ?>
            <p class="text-muted mb-0"><i class="bi bi-palette fs-1"></i></p>
            <p class="text-muted small mb-0"><?php echo e(__('messages.no_crown_color')); ?></p>
        <?php endif; ?>
    </div>
</div>
            
            <div class="card zedan-card mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-calendar-week me-2"></i><?php echo e(__('messages.appointments')); ?></span>
                    <span class="badge bg-secondary"><?php echo e($appointments->total()); ?></span>
                </div>
                <div class="card-body p-0">
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
                                        <th><?php echo e(__('messages.doctor')); ?></th>
                                        <th><?php echo e(__('messages.room')); ?></th>
                                        <th><?php echo e(__('messages.treatment')); ?></th>
                                        <th><?php echo e(__('messages.status')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td data-label="<?php echo e(__('messages.date')); ?>">
                                                <?php echo e($appointment->appointment_date->toDateString()); ?> <?php echo e($appointment->time_range_formatted); ?>

                                                <?php if($appointment->session_number): ?>
                                                    <span class="badge text-bg-info"><?php echo e(__('messages.session')); ?> <?php echo e($appointment->session_number); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="<?php echo e(__('messages.doctor')); ?>"><?php echo e($appointment->doctor->name); ?></td>
                                            <td data-label="<?php echo e(__('messages.room')); ?>"><?php echo e($appointment->room->name); ?></td>
                                            <td data-label="<?php echo e(__('messages.treatment')); ?>"><?php echo e($appointment->treatment?->name ?? '-'); ?></td>
                                            <td data-label="<?php echo e(__('messages.status')); ?>">
                                                <span class="badge text-bg-secondary"><?php echo e(__('messages.status_'.$appointment->status)); ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-2"><?php echo e($appointments->links()); ?></div>
                    <?php endif; ?>
                </div>
            </div>

    
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $patient)): ?>
        <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'editPatientModal','title' => __('messages.edit_patient'),'centered' => true,'scrollable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'editPatientModal','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.edit_patient')),'centered' => true,'scrollable' => true]); ?>
            <form data-ajax-form method="POST" action="<?php echo e(route('patients.update', $patient)); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['name' => 'full_name','label' => __('messages.full_name'),'value' => $patient->full_name,'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'full_name','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.full_name')),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($patient->full_name),'required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $attributes = $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $component = $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['name' => 'phone','label' => __('messages.phone'),'value' => $patient->phone,'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'phone','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.phone')),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($patient->phone),'required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $attributes = $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $component = $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['type' => 'date','name' => 'date_of_birth','label' => __('messages.date_of_birth'),'value' => optional($patient->date_of_birth)->toDateString()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'date','name' => 'date_of_birth','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.date_of_birth')),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(optional($patient->date_of_birth)->toDateString())]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $attributes = $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $component = $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['type' => 'number','name' => 'age','label' => __('messages.age_if_dob_unknown'),'value' => $patient->age]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'number','name' => 'age','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.age_if_dob_unknown')),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($patient->age)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $attributes = $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $component = $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal67ad07a4b593e690d435fee92e6413bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67ad07a4b593e690d435fee92e6413bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-select','data' => ['name' => 'gender','label' => __('messages.gender'),'value' => $patient->gender,'options' => ['male' => __('messages.gender_male'), 'female' => __('messages.gender_female')],'placeholder' => __('messages.select_gender')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'gender','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.gender')),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($patient->gender),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['male' => __('messages.gender_male'), 'female' => __('messages.gender_female')]),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.select_gender'))]); ?>
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
                <?php if (isset($component)) { $__componentOriginalcc0154580828f80bdab5d7fe416ed74a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcc0154580828f80bdab5d7fe416ed74a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-textarea','data' => ['name' => 'address','label' => __('messages.address'),'value' => $patient->address]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'address','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.address')),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($patient->address)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcc0154580828f80bdab5d7fe416ed74a)): ?>
<?php $attributes = $__attributesOriginalcc0154580828f80bdab5d7fe416ed74a; ?>
<?php unset($__attributesOriginalcc0154580828f80bdab5d7fe416ed74a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcc0154580828f80bdab5d7fe416ed74a)): ?>
<?php $component = $__componentOriginalcc0154580828f80bdab5d7fe416ed74a; ?>
<?php unset($__componentOriginalcc0154580828f80bdab5d7fe416ed74a); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalcc0154580828f80bdab5d7fe416ed74a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcc0154580828f80bdab5d7fe416ed74a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-textarea','data' => ['name' => 'notes','label' => __('messages.notes'),'value' => $patient->notes]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'notes','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.notes')),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($patient->notes)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcc0154580828f80bdab5d7fe416ed74a)): ?>
<?php $attributes = $__attributesOriginalcc0154580828f80bdab5d7fe416ed74a; ?>
<?php unset($__attributesOriginalcc0154580828f80bdab5d7fe416ed74a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcc0154580828f80bdab5d7fe416ed74a)): ?>
<?php $component = $__componentOriginalcc0154580828f80bdab5d7fe416ed74a; ?>
<?php unset($__componentOriginalcc0154580828f80bdab5d7fe416ed74a); ?>
<?php endif; ?>

                
                <div class="mb-3">
                    <label class="form-label"><?php echo e(__('messages.photo')); ?></label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                   
                </div>

                
                <div class="mb-3">
                    <label class="form-label"><?php echo e(__('messages.xray_photo')); ?></label>
                    <input type="file" name="xray_photo" class="form-control" accept="image/*">
                </div>

                
                <div class="mb-3">
                    <label class="form-label"><?php echo e(__('messages.crown_color')); ?></label>
                    <input type="text" name="crown_color" class="form-control" value="<?php echo e($patient->crown_color); ?>" placeholder="A2, B1, C3 ...">
                </div>

                <button type="submit" class="btn btn-primary w-100"><?php echo e(__('messages.save')); ?></button>
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

    
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $patient)): ?>
        <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'editToothModal','title' => __('messages.edit_tooth_chart'),'size' => 'xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'editToothModal','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.edit_tooth_chart')),'size' => 'xl']); ?>
            <form data-ajax-form method="POST" action="<?php echo e(route('patients.update', $patient)); ?>">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="tooth-chart-editor">
                    <div class="tooth-arch upper">
                        <?php $__currentLoopData = range(1, 16); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $status = $patient->tooth_chart[$num] ?? 'healthy'; ?>
                            <div class="tooth-editor" data-tooth="<?php echo e($num); ?>">
                                <div class="tooth-body tooth-<?php echo e($status); ?>" onclick="cycleToothStatus(this, <?php echo e($num); ?>)" role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' ')cycleToothStatus(this,<?php echo e($num); ?>)">
                                    <span class="tooth-num"><?php echo e($num); ?></span>
                                </div>
                                <input type="hidden" name="tooth_chart[<?php echo e($num); ?>]" value="<?php echo e($status); ?>" id="tooth-input-<?php echo e($num); ?>">
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="tooth-arch lower">
                        <?php $__currentLoopData = range(17, 32); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $status = $patient->tooth_chart[$num] ?? 'healthy'; ?>
                            <div class="tooth-editor" data-tooth="<?php echo e($num); ?>">
                                <div class="tooth-body tooth-<?php echo e($status); ?>" onclick="cycleToothStatus(this, <?php echo e($num); ?>)" role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' ')cycleToothStatus(this,<?php echo e($num); ?>)">
                                    <span class="tooth-num"><?php echo e($num); ?></span>
                                </div>
                                <input type="hidden" name="tooth_chart[<?php echo e($num); ?>]" value="<?php echo e($status); ?>" id="tooth-input-<?php echo e($num); ?>">
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php
                    $legendStatuses = [
                        'healthy', 'decayed', 'filled', 'crown', 'root_canal',
                        'extracted', 'missing', 'implant', 'fractured', 'abscess',
                        'braces', 'veneer',
                    ];
                ?>
                <div class="tooth-legend mt-3 d-flex flex-wrap gap-2 justify-content-center small">
                    <?php $__currentLoopData = $legendStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="legend-item">
                            <span class="legend-dot tooth-<?php echo e($ls); ?>"></span>
                            <?php echo e(trans('messages.tooth_status_'.$ls, [], 'en')); ?>

                        </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-3"><?php echo e(__('messages.save')); ?></button>
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

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', \App\Models\Payment::class)): ?>
        <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'addPaymentModal','title' => __('messages.record_payment')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'addPaymentModal','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.record_payment'))]); ?>
            <form data-ajax-form method="POST" action="<?php echo e(route('payments.store', $patient)); ?>">
                <?php echo csrf_field(); ?>
                <?php if (isset($component)) { $__componentOriginal67ad07a4b593e690d435fee92e6413bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67ad07a4b593e690d435fee92e6413bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-select','data' => ['name' => 'treatment_id','label' => __('messages.treatment'),'required' => true,'placeholder' => __('messages.select_treatment'),'options' => \App\Models\Treatment::active()->orderBy('name')->pluck('name', 'id')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'treatment_id','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.treatment')),'required' => true,'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.select_treatment')),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\App\Models\Treatment::active()->orderBy('name')->pluck('name', 'id'))]); ?>
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
                <?php if (isset($component)) { $__componentOriginal67ad07a4b593e690d435fee92e6413bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67ad07a4b593e690d435fee92e6413bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-select','data' => ['name' => 'payment_type','label' => __('messages.payment_type'),'required' => true,'options' => ['paid_now' => __('messages.paid_now'), 'pay_later' => __('messages.pay_later'), 'installment' => __('messages.installment')]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'payment_type','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.payment_type')),'required' => true,'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['paid_now' => __('messages.paid_now'), 'pay_later' => __('messages.pay_later'), 'installment' => __('messages.installment')])]); ?>
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
                <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['type' => 'number','step' => '0.01','name' => 'total_amount','label' => __('messages.total_amount'),'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'number','step' => '0.01','name' => 'total_amount','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.total_amount')),'required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $attributes = $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $component = $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['type' => 'number','step' => '0.01','name' => 'first_installment_amount','label' => __('messages.first_installment_amount')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'number','step' => '0.01','name' => 'first_installment_amount','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.first_installment_amount'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $attributes = $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $component = $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-input','data' => ['type' => 'date','name' => 'due_date','label' => __('messages.due_date')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'date','name' => 'due_date','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.due_date'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $attributes = $__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__attributesOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14)): ?>
<?php $component = $__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14; ?>
<?php unset($__componentOriginal93a7e4fbb8709cb7edbcf616ab99cd14); ?>
<?php endif; ?>
                <button type="submit" class="btn btn-primary w-100"><?php echo e(__('messages.save')); ?></button>
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

    
    <?php if($patient->xray_photo): ?>
        <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'xrayViewModal','title' => __('messages.xray_photo'),'size' => 'xl','centered' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'xrayViewModal','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('messages.xray_photo')),'size' => 'xl','centered' => true]); ?>
            <div class="text-center bg-dark p-0" style="margin: -1rem; margin-bottom: 1rem;">
                <img src="<?php echo e($patient->xrayPhotoUrl()); ?>" class="img-fluid" style="max-height: 65vh;" alt="X-Ray Full">
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small font-monospace"><?php echo e(basename($patient->xray_photo)); ?></span>
                <div>
                    <a href="<?php echo e($patient->xrayPhotoUrl()); ?>" download class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-download"></i> <?php echo e(__('messages.download')); ?>

                    </a>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                        <?php echo e(__('messages.close')); ?>

                    </button>
                </div>
            </div>
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

<?php $__env->startPush('styles'); ?>
<style>
.tooth-chart-wrapper, .tooth-chart-editor {
    display: flex;
    flex-direction: column;
    gap: 10px;
    align-items: center;
    padding: 8px;
}
.tooth-arch {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 5px;
    max-width: 100%;
    padding: 8px;
    border-radius: 8px;
}
.tooth-arch.upper {
    border-bottom: 2px solid var(--bs-border-color);
    padding-bottom: 12px;
}
.tooth-arch.lower {
    padding-top: 12px;
}
.tooth, .tooth-editor {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 38px;
}
.tooth-body {
    width: 34px;
    height: 34px;
    border-radius: 50% 50% 45% 45%;
    border: 2px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: default;
    transition: all .2s ease;
    background: #fff;
    position: relative;
}
.tooth-editor .tooth-body {
    cursor: pointer;
}
.tooth-editor .tooth-body:hover,
.tooth-editor .tooth-body:focus {
    transform: scale(1.2);
    box-shadow: 0 3px 8px rgba(0,0,0,.2);
    outline: none;
    z-index: 1;
}
.tooth-num {
    font-size: 10px;
    font-weight: 700;
    color: #495057;
    pointer-events: none;
    line-height: 1;
}
/* Status Colors */
.tooth-healthy   { background: #fff; border-color: #adb5bd; }
.tooth-decayed   { background: #dc3545; border-color: #b02a37; }
.tooth-decayed .tooth-num { color: #fff; }
.tooth-treated   { background: #0d6efd; border-color: #0a58ca; }
.tooth-treated .tooth-num { color: #fff; }
.tooth-missing   { background: #6c757d; border-color: #495057; }
.tooth-missing .tooth-num { color: #fff; }
.tooth-root_canal{ background: #198754; border-color: #146c43; }
.tooth-root_canal .tooth-num { color: #fff; }
.tooth-crown     { background: #ffc107; border-color: #cc9a06; }
.tooth-crown .tooth-num { color: #212529; }

.tooth-legend { gap: 10px; }
.legend-item { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; }
.legend-dot {
    width: 16px; height: 16px;
    border-radius: 50% 50% 45% 45%;
    border: 1px solid #adb5bd;
    display: inline-block;
    flex-shrink: 0;
}
.xray-image {
    border: 1px solid var(--bs-border-color);
    background: #000;
    transition: transform .2s;
}
.xray-image:hover {
    transform: scale(1.02);
}

/*#editPatientModal {
    max-height: calc(100vh - 2rem);
    margin-bottom: 1rem;
    align-items: flex-start !important;
    padding-top: 1rem;
}*/
/* Fix ALL centered modals: sit below top edge with internal scroll */
/*.modal-dialog-centered {
    align-items: flex-start !important;
    min-height: auto !important;
}
.modal-dialog-centered .modal-content {
    max-height: calc(100vh - 3.5rem);
}
.modal-dialog-centered .modal-body {
    overflow-y: auto;
}*/

/* RTL support for tooth chart */
[dir="rtl"] .tooth-arch {
    flex-direction: row-reverse;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function() {
    'use strict';
    const TOOTH_STATUSES = ['healthy', 'decayed', 'treated', 'missing', 'root_canal', 'crown'];
    const STATUS_LABELS = {
        healthy: '<?php echo e(trans("messages.tooth_status_healthy", [], "en")); ?>',
        decayed: '<?php echo e(trans("messages.tooth_status_decayed", [], "en")); ?>',
        treated: '<?php echo e(trans("messages.tooth_status_treated", [], "en")); ?>',
        missing: '<?php echo e(trans("messages.tooth_status_missing", [], "en")); ?>',
        root_canal: '<?php echo e(trans("messages.tooth_status_root_canal", [], "en")); ?>',
        crown: '<?php echo e(trans("messages.tooth_status_crown", [], "en")); ?>'
    };

    window.cycleToothStatus = function(el, num) {
        const input = document.getElementById('tooth-input-' + num);
        if (!input) return;

        let current = input.value || 'healthy';
        let idx = TOOTH_STATUSES.indexOf(current);
        if (idx === -1) idx = 0;
        let next = TOOTH_STATUSES[(idx + 1) % TOOTH_STATUSES.length];

        input.value = next;
        TOOTH_STATUSES.forEach(s => el.classList.remove('tooth-' + s));
        el.classList.add('tooth-' + next);

        // Update tooltip/title if needed
        const toothName = el.closest('.tooth-editor')?.getAttribute('data-tooth') || num;
        el.setAttribute('title', 'Tooth #' + toothName + ' — ' + (STATUS_LABELS[next] || next));
    };
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/patients/show.blade.php ENDPATH**/ ?>