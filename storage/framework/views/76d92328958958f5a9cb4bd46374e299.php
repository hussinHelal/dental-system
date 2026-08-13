<?php if (isset($component)) { $__componentOriginalcb8170ac00b272413fe5b25f86fc5e3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcb8170ac00b272413fe5b25f86fc5e3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.guest-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <?php echo e($errors->first()); ?>

        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('login')); ?>">
        <?php echo csrf_field(); ?>

        <div class="input-group mb-3">
            <input type="text" name="username" id="username" class="form-control" placeholder='<?php echo e(__('messages.username')); ?>' value="<?php echo e(old('username')); ?>" required autofocus>
        </div>

        <div class="input-group mb-3">
            <input type="password" name="password" id="password" class="form-control" placeholder="<?php echo e(__('messages.password')); ?>" required>
            <button type="button" id="toggle-password" class="btn btn-outline-secondary" aria-label="<?php echo e(__('messages.showPassword')); ?>" > 
                <i class="fa-solid fa-eye" id="toggle-icon" > </i>  
            </button>  
        </div>

        <div class="mb-3 form-check"> 
            <input type="checkbox" name="remember" id="remember" class="form-check-input"> 
            <label for="remember" class="form-check-label"><?php echo e(__('messages.remember_me')); ?></label> 
        </div> 

        <button type="submit" class="btn btn-primary w-100"><?php echo e(__('messages.login')); ?></button> 
    </form> 
    
   
<script>
  document.addEventListener('DOMContentLoaded', function() {
      const toggleBtn = document.getElementById('toggle-password');
      const passwordInput = document.getElementById('password');
      const toggleIcon = document.getElementById('toggle-icon');
  
    toggleBtn.addEventListener('click', function() {
        const isPassword = passwordInput.getAttribute('type') === 'password';
  
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
  
        if (isPassword) {
              toggleIcon.classList.remove('fa-eye');
              toggleIcon.classList.add('fa-eye-slash');
        } else {
              toggleIcon.classList.remove('fa-eye-slash');
              toggleIcon.classList.add('fa-eye');
        }
    });
});
</script>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcb8170ac00b272413fe5b25f86fc5e3a)): ?>
<?php $attributes = $__attributesOriginalcb8170ac00b272413fe5b25f86fc5e3a; ?>
<?php unset($__attributesOriginalcb8170ac00b272413fe5b25f86fc5e3a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcb8170ac00b272413fe5b25f86fc5e3a)): ?>
<?php $component = $__componentOriginalcb8170ac00b272413fe5b25f86fc5e3a; ?>
<?php unset($__componentOriginalcb8170ac00b272413fe5b25f86fc5e3a); ?>
<?php endif; ?> 
<?php /**PATH C:\Users\hussin\Downloads\zedan-dental-clinic\zedan\resources\views/auth/login.blade.php ENDPATH**/ ?>