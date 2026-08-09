<x-guest-layout>
    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="input-group mb-3">
            <input type="text" name="username" id="username" class="form-control" placeholder='{{ __('messages.username') }}' value="{{ old('username') }}" required autofocus>
        </div>

        <div class="input-group mb-3">
            <input type="password" name="password" id="password" class="form-control" placeholder="{{ __('messages.password') }}" required>
            <button type="button" id="toggle-password" class="btn btn-outline-secondary" aria-label="{{ __('messages.showPassword') }}" > 
                <i class="fa-solid fa-eye" id="toggle-icon" > </i>  
            </button>  
        </div>

        <div class="mb-3 form-check"> 
            <input type="checkbox" name="remember" id="remember" class="form-check-input"> 
            <label for="remember" class="form-check-label">{{ __('messages.remember_me') }}</label> 
        </div> 

        <button type="submit" class="btn btn-primary w-100">{{ __('messages.login') }}</button> 
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

</x-guest-layout> 
