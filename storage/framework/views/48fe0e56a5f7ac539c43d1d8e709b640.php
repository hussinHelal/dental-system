<?php $contract = $contract ?? null; ?>

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label"><?php echo e(__('insurance.company_name')); ?></label>
        <input type="text" name="company_name" class="form-control" required value="<?php echo e(old('company_name', $contract->company_name ?? '')); ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label"><?php echo e(__('insurance.contract_number')); ?></label>
        <input type="text" name="contract_number" class="form-control" value="<?php echo e(old('contract_number', $contract->contract_number ?? '')); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label"><?php echo e(__('insurance.start_date')); ?></label>
        <input type="date" name="start_date" class="form-control" required
            value="<?php echo e(old('start_date', optional($contract?->start_date)->format('Y-m-d'))); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label"><?php echo e(__('insurance.end_date')); ?></label>
        <input type="date" name="end_date" class="form-control" required
            value="<?php echo e(old('end_date', optional($contract?->end_date)->format('Y-m-d'))); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label"><?php echo e(__('insurance.discount')); ?></label>
        <input type="number" step="0.01" min="0" max="100" name="discount_percentage" class="form-control" required
            value="<?php echo e(old('discount_percentage', $contract->discount_percentage ?? 0)); ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label"><?php echo e(__('insurance.contact_person')); ?></label>
        <div class="position-relative" data-patient-autocomplete data-endpoint="<?php echo e(url('/patients/search')); ?>" data-target-phone="#insurancePhone<?php echo e($contract?->id ?? 'Create'); ?>">
            <input type="text" name="contact_person" class="form-control" value="<?php echo e(old('contact_person', $contract->contact_person ?? '')); ?>" data-patient-search-input autocomplete="off">
            <div class="dropdown-menu w-100 p-0 shadow-sm d-none" data-patient-results></div>
        </div>
        <div class="form-text"><?php echo e(__('insurance.contact_person_hint')); ?></div>
    </div>
    <div class="col-md-6">
        <label class="form-label"><?php echo e(__('insurance.phone')); ?></label>
        <input id="insurancePhone<?php echo e($contract?->id ?? 'Create'); ?>" type="text" name="phone" class="form-control" value="<?php echo e(old('phone', $contract->phone ?? '')); ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label"><?php echo e(__('insurance.status')); ?></label>
        <select name="status" class="form-select" required>
            <option value="active" <?php if(old('status', $contract->status->value ?? 'active') === 'active'): echo 'selected'; endif; ?>><?php echo e(__('insurance.status_active')); ?></option>
            <option value="expired" <?php if(old('status', $contract->status->value ?? '') === 'expired'): echo 'selected'; endif; ?>><?php echo e(__('insurance.status_expired')); ?></option>
            <option value="cancelled" <?php if(old('status', $contract->status->value ?? '') === 'cancelled'): echo 'selected'; endif; ?>><?php echo e(__('insurance.status_cancelled')); ?></option>
        </select>
    </div>
    <div class="col-12">
        <label class="form-label"><?php echo e(__('insurance.coverage_details')); ?></label>
        <textarea name="coverage_details" class="form-control" rows="2"><?php echo e(old('coverage_details', $contract->coverage_details ?? '')); ?></textarea>
    </div>
    <div class="col-12">
        <label class="form-label"><?php echo e(__('insurance.notes')); ?></label>
        <textarea name="notes" class="form-control" rows="2"><?php echo e(old('notes', $contract->notes ?? '')); ?></textarea>
    </div>
</div>
<?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/insurance/partials/fields.blade.php ENDPATH**/ ?>