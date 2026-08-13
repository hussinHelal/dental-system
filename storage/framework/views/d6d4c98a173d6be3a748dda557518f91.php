<?php $lab = $lab ?? null; ?>

<div class="mb-3">
    <label class="form-label"><?php echo e(__('dental_labs.name')); ?></label>
    <input type="text" name="name" class="form-control" required value="<?php echo e(old('name', $lab->name ?? '')); ?>">
</div>
<div class="mb-3">
    <label class="form-label"><?php echo e(__('dental_labs.phone')); ?></label>
    <input type="text" name="phone" class="form-control" value="<?php echo e(old('phone', $lab->phone ?? '')); ?>">
</div>
<div class="mb-3">
    <label class="form-label"><?php echo e(__('dental_labs.address')); ?></label>
    <input type="text" name="address" class="form-control" value="<?php echo e(old('address', $lab->address ?? '')); ?>">
</div>
<div class="mb-3">
    <label class="form-label"><?php echo e(__('dental_labs.notes')); ?></label>
    <textarea name="notes" class="form-control" rows="2"><?php echo e(old('notes', $lab->notes ?? '')); ?></textarea>
</div>
<?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/dental-labs/partials/fields.blade.php ENDPATH**/ ?>