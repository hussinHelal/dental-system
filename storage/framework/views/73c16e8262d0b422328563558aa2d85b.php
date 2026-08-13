<?php $__env->startSection('title', __('reports.title')); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-0">
        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <ul class="mb-0 ps-3"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3 shadow-sm p-3 rounded-4 zedan-page-header">
            <div class="d-flex align-items-center gap-2"><div class="rounded-circle bg-primary-subtle p-2"><i class="bi bi-bar-chart-line text-primary"></i></div><h3 class="mb-0"><?php echo e(__('reports.title')); ?></h3></div>
            <form method="GET" class="d-flex flex-wrap align-items-end gap-2">
                <div><label class="form-label small mb-1"><?php echo e(__('reports.from')); ?></label><input type="date" name="from" value="<?php echo e($from); ?>" class="form-control"></div>
                <div><label class="form-label small mb-1"><?php echo e(__('reports.to')); ?></label><input type="date" name="to" value="<?php echo e($to); ?>" class="form-control"></div>
                <button class="btn btn-outline-primary" type="submit"><?php echo e(__('reports.filter')); ?></button>
                <div class="d-flex gap-1"><a class="btn btn-outline-secondary" href="<?php echo e(route('reports.export-excel', ['from' => $from, 'to' => $to])); ?>"><i class="bi bi-file-earmark-spreadsheet me-1"></i><?php echo e(__('reports.export_excel')); ?></a><a class="btn btn-outline-secondary" href="<?php echo e(route('reports.export-pdf', ['from' => $from, 'to' => $to])); ?>"><i class="bi bi-file-earmark-pdf me-1"></i><?php echo e(__('reports.export_pdf')); ?></a></div>
            </form>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="card zedan-card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small"><?php echo e(__('reports.total_income')); ?></div><div class="fs-4 fw-bold text-success"><?php echo e(number_format($totals['income'], 2)); ?></div></div></div></div>
            <div class="col-md-4"><div class="card zedan-card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small"><?php echo e(__('reports.total_expense')); ?></div><div class="fs-4 fw-bold text-danger"><?php echo e(number_format($totals['expense'], 2)); ?></div></div></div></div>
            <div class="col-md-4"><div class="card zedan-card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small"><?php echo e(__('reports.net_balance')); ?></div><div class="fs-4 fw-bold <?php echo e($totals['net'] >= 0 ? 'text-primary' : 'text-danger'); ?>"><?php echo e(number_format($totals['net'], 2)); ?></div></div></div></div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6"><div class="card zedan-card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small"><?php echo e(__('reports.purchases_total')); ?></div><div class="fs-5 fw-bold"><?php echo e(number_format($stats['purchases_total'], 2)); ?></div></div></div></div>
            <div class="col-md-3 col-6"><div class="card zedan-card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small"><?php echo e(__('reports.open_lab_cases')); ?></div><div class="fs-5 fw-bold"><?php echo e($stats['open_lab_cases']); ?></div></div></div></div>
            <div class="col-md-3 col-6"><div class="card zedan-card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small"><?php echo e(__('reports.assets_book_value')); ?></div><div class="fs-5 fw-bold"><?php echo e(number_format($stats['assets_book_value'], 2)); ?></div></div></div></div>
            <div class="col-md-3 col-6"><div class="card zedan-card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small"><?php echo e(__('reports.active_insurance_contracts')); ?></div><div class="fs-5 fw-bold"><?php echo e($stats['active_insurance_contracts']); ?></div></div></div></div>
        </div>

        <div class="card zedan-card shadow-sm"><div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2"><h5 class="card-title mb-0"><?php echo e(__('reports.monthly_trend')); ?></h5><div class="d-flex gap-3 small"><span><span class="d-inline-block rounded-1 bg-success" style="width:12px;height:12px;"></span> <?php echo e(__('reports.income')); ?></span><span><span class="d-inline-block rounded-1 bg-danger" style="width:12px;height:12px;"></span> <?php echo e(__('reports.expense')); ?></span></div></div>
            <div class="w-100"><svg id="monthlyChart" viewBox="0 0 760 260" style="width:100%;height:auto;min-height:260px;" role="img" aria-label="<?php echo e(__('reports.monthly_trend')); ?>"></svg></div>
        </div></div>
    </div>

    <script src="<?php echo e(asset('js/reports-monthly-chart.js')); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            renderMonthlyChart('monthlyChart', <?php echo json_encode($monthly, 15, 512) ?>, { noData: <?php echo json_encode(__('reports.no_data'), 15, 512) ?> });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/reports/index.blade.php ENDPATH**/ ?>