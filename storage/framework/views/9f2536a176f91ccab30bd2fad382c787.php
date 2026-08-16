<?php $purchase = $purchase ?? null; ?>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label class="form-label"><?php echo e(__('purchases.supplier')); ?></label>
        <select name="supplier_id" class="form-select" required>
            <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($supplier->id); ?>" <?php if(old('supplier_id', $purchase->supplier_id ?? '') == $supplier->id): echo 'selected'; endif; ?>><?php echo e($supplier->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label"><?php echo e(__('purchases.date')); ?></label>
        <input type="date" name="purchase_date" class="form-control" required
            value="<?php echo e(old('purchase_date', optional($purchase?->purchase_date)->format('Y-m-d'))); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label"><?php echo e(__('purchases.payment_status')); ?></label>
        <select name="payment_status" class="form-select" required>
            <option value="unpaid" <?php if(old('payment_status', $purchase->payment_status->value ?? 'unpaid') === 'unpaid'): echo 'selected'; endif; ?>><?php echo e(__('purchases.unpaid')); ?></option>
            <option value="partial" <?php if(old('payment_status', $purchase->payment_status->value ?? '') === 'partial'): echo 'selected'; endif; ?>><?php echo e(__('purchases.partial')); ?></option>
            <option value="paid" <?php if(old('payment_status', $purchase->payment_status->value ?? '') === 'paid'): echo 'selected'; endif; ?>><?php echo e(__('purchases.paid')); ?></option>
        </select>
    </div>
</div>

<label class="form-label"><?php echo e(__('purchases.items')); ?></label>
<table class="table table-sm" id="<?php echo e($tableId); ?>">
    <thead>
        <tr>
            <th><?php echo e(__('purchases.item_name')); ?></th>
            <th><?php echo e(__('purchases.quantity')); ?></th>
            <th><?php echo e(__('purchases.unit_price')); ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<button type="button" class="btn btn-sm btn-outline-secondary mb-3" onclick="addPurchaseItemRow('<?php echo e($tableId); ?>')">
    + <?php echo e(__('purchases.add_item')); ?>

</button>

<div class="mb-3">
    <label class="form-label"><?php echo e(__('purchases.notes')); ?></label>
    <textarea name="notes" class="form-control" rows="2"><?php echo e(old('notes', $purchase->notes ?? '')); ?></textarea>
</div>
<?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/purchases/partials/fields.blade.php ENDPATH**/ ?>