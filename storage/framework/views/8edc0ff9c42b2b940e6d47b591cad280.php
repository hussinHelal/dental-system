<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name')); ?> &middot; <?php echo e(__('messages.login')); ?></title>

    <?php if(app()->getLocale() === 'ar'): ?>
        <?php echo app('Illuminate\Foundation\Vite')('resources/css/app-rtl.css'); ?>
    <?php else: ?>
        <?php echo app('Illuminate\Foundation\Vite')('resources/css/app.css'); ?>
    <?php endif; ?>
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh; background: linear-gradient(135deg, var(--zedan-primary), var(--zedan-accent));">
    <div class="position-absolute top-0 end-0 p-3">
        <div class="dropdown">
            <button class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown">
                <?php echo e(app()->getLocale() === 'ar' ? 'العربية' : 'English'); ?>

            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); fetch('/locale',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},body:JSON.stringify({locale:'en'})}).finally(()=>location.reload());">English</a></li>
                <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); fetch('/locale',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},body:JSON.stringify({locale:'ar'})}).finally(()=>location.reload());">العربية</a></li>
            </ul>
        </div>
    </div>

    <div class="card zedan-card shadow-lg" style="width: 100%; max-width: 400px;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <i class="bi bi-clipboard2-pulse text-primary" style="font-size: 2.5rem;"></i>
                <h4 class="mt-2 mb-0"><?php echo e(config('app.name')); ?></h4>
                <p class="text-secondary small"><?php echo e(__('messages.login_subtitle')); ?></p>
            </div>

            <?php echo e($slot); ?>

        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/components/guest-layout.blade.php ENDPATH**/ ?>