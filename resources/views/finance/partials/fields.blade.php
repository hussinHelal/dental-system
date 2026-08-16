@php $transaction = $transaction ?? null; @endphp

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">{{ __('finance.type') }}</label>
        <select name="type" class="form-select" required>
            <option value="income" @selected(old('type', $transaction->type->value ?? 'expense') === 'income')>{{ __('reports.income') }}</option>
            <option value="expense" @selected(old('type', $transaction->type->value ?? 'expense') === 'expense')>{{ __('reports.expense') }}</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('finance.amount') }}</label>
        <input type="text" inputmode="decimal" name="amount" class="form-control" required
            value="{{ old('amount', $transaction->amount ?? '') }}"
            data-money-input placeholder="500" autocomplete="off">
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('finance.transaction_date') }}</label>
        <input type="date" name="transaction_date" class="form-control" required
            value="{{ old('transaction_date', optional($transaction?->transaction_date)->format('Y-m-d')) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('finance.category') }}</label>
        <input type="text" name="category" class="form-control"
            value="{{ old('category', $transaction->category ?? '') }}"
            list="financeCategoryOptions" placeholder="{{ __('finance.category_placeholder') }}">
        <datalist id="financeCategoryOptions">
            <option value="Rent"><option value="Salaries"><option value="Utilities">
            <option value="Supplies"><option value="Lab Fees"><option value="Maintenance">
            <option value="Patient Payment"><option value="Other Income"><option value="Other Expense">
        </datalist>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('finance.payment_method') }}</label>
        <select name="payment_method" class="form-select">
            <option value="">{{ __('finance.not_specified') }}</option>
            <option value="cash" @selected(old('payment_method', $transaction->payment_method ?? '') === 'cash')>{{ __('finance.cash') }}</option>
            <option value="bank" @selected(old('payment_method', $transaction->payment_method ?? '') === 'bank')>{{ __('finance.bank') }}</option>
            <option value="other" @selected(old('payment_method', $transaction->payment_method ?? '') === 'other')>{{ __('finance.other') }}</option>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label">{{ __('finance.description') }}</label>
        <textarea name="description" class="form-control" rows="2" maxlength="1000">{{ old('description', $transaction->description ?? '') }}</textarea>
    </div>
</div>
