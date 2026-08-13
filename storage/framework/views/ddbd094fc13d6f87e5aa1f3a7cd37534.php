<?php $asset = $asset ?? null; ?>

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label"><?php echo e(__('assets.name')); ?></label>
        <input type="text" name="name" class="form-control" required value="<?php echo e(old('name', $asset->name ?? '')); ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label"><?php echo e(__('assets.category')); ?></label>
        <input type="text" name="category" class="form-control" value="<?php echo e(old('category', $asset->category ?? '')); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label"><?php echo e(__('assets.purchase_date')); ?></label>
        <input type="date" name="purchase_date" class="form-control" required
            value="<?php echo e(old('purchase_date', optional($asset?->purchase_date)->format('Y-m-d'))); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label"><?php echo e(__('assets.purchase_cost')); ?></label>
        <input type="text" inputmode="decimal" name="purchase_cost" class="form-control" required value="<?php echo e(old('purchase_cost', $asset->purchase_cost ?? '')); ?>" data-money-input placeholder="10k" autocomplete="off">
    </div>
    <div class="col-md-4">
        <label class="form-label"><?php echo e(__('assets.salvage_value')); ?></label>
        <input type="text" inputmode="decimal" name="salvage_value" class="form-control" required value="<?php echo e(old('salvage_value', $asset->salvage_value ?? 0)); ?>" data-money-input placeholder="0" autocomplete="off">
    </div>
    <div class="col-md-4">
        <label class="form-label"><?php echo e(__('assets.useful_life_years')); ?></label>
        <input type="number" min="1" max="100" name="useful_life_years" class="form-control" required value="<?php echo e(old('useful_life_years', $asset->useful_life_years ?? 5)); ?>">
    </div>
    <div class="col-md-8">
        <label class="form-label"><?php echo e(__('assets.attachment')); ?></label>
        <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
        <?php if($asset?->attachment_path): ?>
            <div class="form-text"><a href="<?php echo e(Storage::url($asset->attachment_path)); ?>" target="_blank"><?php echo e(__('assets.view_current_attachment')); ?></a></div>
        <?php endif; ?>
    </div>
    <div class="col-12">
        <label class="form-label"><?php echo e(__('assets.notes')); ?></label>
        <textarea name="notes" class="form-control" rows="2"><?php echo e(old('notes', $asset->notes ?? '')); ?></textarea>
    </div>
</div>
<?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/assets/partials/fields.blade.php ENDPATH**/ ?>