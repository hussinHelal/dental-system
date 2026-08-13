<?php $supplier = $supplier ?? null; ?>

<div class="mb-3">
    <label class="form-label"><?php echo e(__('suppliers.name')); ?></label>
    <input type="text" name="name" class="form-control" required value="<?php echo e(old('name', $supplier->name ?? '')); ?>">
</div>
<div class="mb-3">
    <label class="form-label"><?php echo e(__('suppliers.phone')); ?></label>
    <input type="text" name="phone" class="form-control" value="<?php echo e(old('phone', $supplier->phone ?? '')); ?>">
</div>
<div class="mb-3">
    <label class="form-label"><?php echo e(__('suppliers.address')); ?></label>
    <input type="text" name="address" class="form-control" value="<?php echo e(old('address', $supplier->address ?? '')); ?>">
</div>
<div class="mb-3">
    <label class="form-label"><?php echo e(__('suppliers.notes')); ?></label>
    <textarea name="notes" class="form-control" rows="2"><?php echo e(old('notes', $supplier->notes ?? '')); ?></textarea>
</div>
<?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/suppliers/partials/fields.blade.php ENDPATH**/ ?>