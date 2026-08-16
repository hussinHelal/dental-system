<?php $transaction = $transaction ?? null; ?>

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label"><?php echo e(__('finance.type')); ?></label>
        <select name="type" class="form-select" required>
            <option value="income" <?php if(old('type', $transaction->type->value ?? 'expense') === 'income'): echo 'selected'; endif; ?>><?php echo e(__('reports.income')); ?></option>
            <option value="expense" <?php if(old('type', $transaction->type->value ?? 'expense') === 'expense'): echo 'selected'; endif; ?>><?php echo e(__('reports.expense')); ?></option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label"><?php echo e(__('finance.amount')); ?></label>
        <input type="text" inputmode="decimal" name="amount" class="form-control" required
            value="<?php echo e(old('amount', $transaction->amount ?? '')); ?>"
            data-money-input placeholder="500" autocomplete="off">
    </div>
    <div class="col-md-4">
        <label class="form-label"><?php echo e(__('finance.transaction_date')); ?></label>
        <input type="date" name="transaction_date" class="form-control" required
            value="<?php echo e(old('transaction_date', optional($transaction?->transaction_date)->format('Y-m-d'))); ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label"><?php echo e(__('finance.category')); ?></label>
        <input type="text" name="category" class="form-control"
            value="<?php echo e(old('category', $transaction->category ?? '')); ?>"
            list="financeCategoryOptions" placeholder="<?php echo e(__('finance.category_placeholder')); ?>">
        <datalist id="financeCategoryOptions">
            <option value="Rent"><option value="Salaries"><option value="Utilities">
            <option value="Supplies"><option value="Lab Fees"><option value="Maintenance">
            <option value="Patient Payment"><option value="Other Income"><option value="Other Expense">
        </datalist>
    </div>
    <div class="col-md-6">
        <label class="form-label"><?php echo e(__('finance.payment_method')); ?></label>
        <select name="payment_method" class="form-select">
            <option value=""><?php echo e(__('finance.not_specified')); ?></option>
            <option value="cash" <?php if(old('payment_method', $transaction->payment_method ?? '') === 'cash'): echo 'selected'; endif; ?>><?php echo e(__('finance.cash')); ?></option>
            <option value="bank" <?php if(old('payment_method', $transaction->payment_method ?? '') === 'bank'): echo 'selected'; endif; ?>><?php echo e(__('finance.bank')); ?></option>
            <option value="other" <?php if(old('payment_method', $transaction->payment_method ?? '') === 'other'): echo 'selected'; endif; ?>><?php echo e(__('finance.other')); ?></option>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label"><?php echo e(__('finance.description')); ?></label>
        <textarea name="description" class="form-control" rows="2" maxlength="1000"><?php echo e(old('description', $transaction->description ?? '')); ?></textarea>
    </div>
</div>
<?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/finance/partials/fields.blade.php ENDPATH**/ ?>