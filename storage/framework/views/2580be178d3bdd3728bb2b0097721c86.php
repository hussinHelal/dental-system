

<?php $__env->startSection('title', $patient->full_name . ' - ' . __('messages.tooth_chart')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><?php echo e(__('messages.tooth_chart')); ?></h2>
            <p class="text-muted mb-0"><?php echo e($patient->full_name); ?> — <?php echo e($patient->phone); ?></p>
        </div>
        <a href="<?php echo e(route('patients.show', $patient)); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> <?php echo e(__('messages.back_to_patient')); ?>

        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="tooth-chart-container">
                        <!-- Upper Arch -->
                        <div class="arch upper-arch mb-4">
                            <h6 class="text-center text-muted mb-3"><?php echo e(__('messages.upper_arch')); ?></h6>
                            <div class="teeth-rows-wrapper">
                                <div class="side-label small text-muted"><?php echo e(__('messages.left')); ?></div>
                                <div class="teeth-row upper-left">
                                <?php $__currentLoopData = [9,10,11,12,13,14,15,16]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo $__env->make('patients.partials.tooth', [
                                        'number' => $num,
                                        'record' => $toothMap[$num] ?? null,
                                        'label' => $loop->iteration,
                                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <div class="teeth-row upper-right">
                                <?php $__currentLoopData = [1,2,3,4,5,6,7,8]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo $__env->make('patients.partials.tooth', [
                                        'number' => $num,
                                        'record' => $toothMap[$num] ?? null,
                                        'label' => $loop->iteration,
                                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <div class="side-label small text-muted"><?php echo e(__('messages.right')); ?></div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="arch-divider"></div>

                        <!-- Lower Arch -->
                        <div class="arch lower-arch mt-4">
                            <h6 class="text-center text-muted mb-3"><?php echo e(__('messages.lower_arch')); ?></h6>
                            <div class="teeth-rows-wrapper">
                                <div class="side-label small text-muted"><?php echo e(__('messages.left')); ?></div>
                                <div class="teeth-row lower-left">
                                <?php $__currentLoopData = [17,18,19,20,21,22,23,24]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo $__env->make('patients.partials.tooth', [
                                        'number' => $num,
                                        'record' => $toothMap[$num] ?? null,
                                        'label' => $loop->iteration,
                                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <div class="teeth-row lower-right">
                                <?php $__currentLoopData = [25,26,27,28,29,30,31,32]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo $__env->make('patients.partials.tooth', [
                                        'number' => $num,
                                        'record' => $toothMap[$num] ?? null,
                                        'label' => $loop->iteration,
                                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <div class="side-label small text-muted"><?php echo e(__('messages.right')); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend & Quick Actions -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><?php echo e(__('messages.legend')); ?></h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <?php $__currentLoopData = [
                            'healthy' => ['success', __('messages.status_healthy')],
                            'decayed' => ['danger', __('messages.status_decayed')],
                            'filled' => ['primary', __('messages.status_filled')],
                            'crown' => ['warning', __('messages.status_crown')],
                            'root_canal' => ['info', __('messages.status_root_canal')],
                            'extracted' => ['secondary', __('messages.status_extracted')],
                            'missing' => ['dark', __('messages.status_missing')],
                            'implant' => ['success', __('messages.status_implant')],
                            'fractured' => ['danger', __('messages.status_fractured')],
                            'abscess' => ['danger', __('messages.status_abscess')],
                            'braces' => ['primary', __('messages.status_braces')],
                            'veneer' => ['warning', __('messages.status_veneer')],
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => [$color, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="badge bg-<?php echo e($color); ?>"><?php echo e($label); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <!-- Selected Tooth Info -->
            <div class="card shadow-sm" id="toothInfoCard" style="display: none;">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0"><?php echo e(__('messages.tooth')); ?> #<span id="selectedToothNum">--</span></h6>
                </div>
                <div class="card-body">
                    <form id="toothForm" data-ajax-form method="POST" action="<?php echo e(route('patients.tooth-chart.update', $patient)); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="tooth_number" id="toothNumberInput">
                        
                        <div class="mb-3">
                            <label class="form-label"><?php echo e(__('messages.status')); ?></label>
                            <select name="status" class="form-select" id="toothStatusSelect">
                                <option value="healthy"><?php echo e(__('messages.status_healthy')); ?></option>
                                <option value="decayed"><?php echo e(__('messages.status_decayed')); ?></option>
                                <option value="filled"><?php echo e(__('messages.status_filled')); ?></option>
                                <option value="crown"><?php echo e(__('messages.status_crown')); ?></option>
                                <option value="root_canal"><?php echo e(__('messages.status_root_canal')); ?></option>
                                <option value="extracted"><?php echo e(__('messages.status_extracted')); ?></option>
                                <option value="missing"><?php echo e(__('messages.status_missing')); ?></option>
                                <option value="implant"><?php echo e(__('messages.status_implant')); ?></option>
                                <option value="fractured"><?php echo e(__('messages.status_fractured')); ?></option>
                                <option value="abscess"><?php echo e(__('messages.status_abscess')); ?></option>
                                <option value="braces"><?php echo e(__('messages.status_braces')); ?></option>
                                <option value="veneer"><?php echo e(__('messages.status_veneer')); ?></option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><?php echo e(__('messages.linked_treatment')); ?></label>
                            <select name="treatment_id" class="form-select">
                                <option value=""><?php echo e(__('messages.none')); ?></option>
                                <?php $__currentLoopData = $treatments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $treatment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($treatment->id); ?>"><?php echo e($treatment->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><?php echo e(__('messages.notes')); ?></label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-check-lg"></i> <?php echo e(__('messages.save')); ?>

                            </button>
                            <button type="button" class="btn btn-outline-danger" id="resetToothBtn">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.tooth-chart-container {
    user-select: none;
}

.arch-divider {
    height: 3px;
    background: linear-gradient(90deg, transparent, var(--bs-border-color), transparent);
    margin: 1rem 0;
    border-radius: 2px;
}

.teeth-row {
    display: flex;
    justify-content: center;
    gap: 4px;
    flex-wrap: wrap;
    margin-bottom: 4px;
}

.teeth-rows-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    width: 100%;
}

.side-label {
    width: 48px;
    text-align: center;
    opacity: 0.7;
}

.tooth-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    transition: transform 0.15s ease;
    padding: 4px;
    border-radius: 8px;
}

.tooth-wrapper:hover {
    transform: translateY(-2px);
    background: rgba(var(--bs-primary-rgb), 0.05);
}

.tooth-wrapper.selected {
    background: rgba(var(--bs-primary-rgb), 0.15);
    box-shadow: 0 0 0 2px var(--bs-primary);
}

.tooth-wrapper.extracted .tooth-svg,
.tooth-wrapper.missing .tooth-svg {
    opacity: 0.2;
    filter: grayscale(1);
}

.tooth-number {
    font-size: 0.65rem;
    font-weight: 600;
    color: var(--bs-secondary);
    margin-top: 2px;
}

.tooth-svg {
    width: 36px;
    height: 48px;
    filter: drop-shadow(0 1px 2px rgba(0,0,0,0.1));
}

.tooth-svg path {
    transition: fill 0.2s ease, stroke 0.2s ease;
}

/* Status colors */
.tooth-wrapper.healthy .tooth-svg path { fill: #e8f5e9; stroke: #4caf50; }
.tooth-wrapper.decayed .tooth-svg path { fill: #ffebee; stroke: #f44336; }
.tooth-wrapper.filled .tooth-svg path { fill: #e3f2fd; stroke: #2196f3; }
.tooth-wrapper.crown .tooth-svg path { fill: #fff8e1; stroke: #ff9800; }
.tooth-wrapper.root_canal .tooth-svg path { fill: #e1f5fe; stroke: #03a9f4; }
.tooth-wrapper.extracted .tooth-svg path { fill: #f5f5f5; stroke: #9e9e9e; }
.tooth-wrapper.missing .tooth-svg path { fill: #fafafa; stroke: #bdbdbd; }
.tooth-wrapper.implant .tooth-svg path { fill: #e8f5e9; stroke: #2e7d32; }
.tooth-wrapper.fractured .tooth-svg path { fill: #ffebee; stroke: #c62828; }
.tooth-wrapper.abscess .tooth-svg path { fill: #fce4ec; stroke: #ad1457; }
.tooth-wrapper.braces .tooth-svg path { fill: #e8eaf6; stroke: #3f51b5; }
.tooth-wrapper.veneer .tooth-svg path { fill: #fff3e0; stroke: #e65100; }

@media (max-width: 768px) {
    .tooth-svg { width: 28px; height: 38px; }
    .teeth-row { gap: 2px; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const toothWrappers = document.querySelectorAll('.tooth-wrapper');
    const infoCard = document.getElementById('toothInfoCard');
    const selectedNumEl = document.getElementById('selectedToothNum');
    const toothNumInput = document.getElementById('toothNumberInput');
    const statusSelect = document.getElementById('toothStatusSelect');
    const form = document.getElementById('toothForm');
    const resetBtn = document.getElementById('resetToothBtn');

    let currentTooth = null;

    toothWrappers.forEach(wrapper => {
        wrapper.addEventListener('click', () => {
            // Deselect previous
            toothWrappers.forEach(t => t.classList.remove('selected'));
            
            // Select current
            wrapper.classList.add('selected');
            currentTooth = wrapper;
            
            const num = wrapper.dataset.tooth;
            const label = wrapper.dataset.label || num;
            const status = wrapper.dataset.status || 'healthy';
            const treatmentId = wrapper.dataset.treatment || '';
            const notes = wrapper.dataset.notes || '';

            selectedNumEl.textContent = label;
            toothNumInput.value = num;
            statusSelect.value = status;
            form.querySelector('select[name="treatment_id"]').value = treatmentId;
            form.querySelector('textarea[name="notes"]').value = notes;

            infoCard.style.display = 'block';
            infoCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });

    resetBtn.addEventListener('click', async () => {
        if (!currentTooth) return;
        if (!confirm('<?php echo e(__('messages.confirm_reset_tooth')); ?>')) return;

        const num = currentTooth.dataset.tooth;
        const url = `<?php echo e(route('patients.tooth-chart.destroy', [$patient, '__NUM__'])); ?>`.replace('__NUM__', num);

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: new URLSearchParams({ _method: 'DELETE' })
            });

            if (response.ok) {
                currentTooth.className = 'tooth-wrapper healthy';
                currentTooth.dataset.status = 'healthy';
                delete currentTooth.dataset.treatment;
                delete currentTooth.dataset.notes;
                infoCard.style.display = 'none';
            }
        } catch (err) {
            console.error('Reset failed', err);
        }
    });

    // Handle form success to update UI without reload
    form.addEventListener('ajax:success', (e) => {
        if (!currentTooth) return;
        const status = statusSelect.value;
        currentTooth.className = `tooth-wrapper selected ${status}`;
        currentTooth.dataset.status = status;
        currentTooth.dataset.treatment = form.querySelector('select[name="treatment_id"]').value;
        currentTooth.dataset.notes = form.querySelector('textarea[name="notes"]').value;
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/patients/tooth-chart.blade.php ENDPATH**/ ?>