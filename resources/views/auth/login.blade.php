<x-guest-layout>
    <div class="auth-title">Welcome back</div>
    <div class="auth-sub">Sign in to your SubTrack account</div>

    @if (session('status'))
        <div class="status-msg">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">Email address</label>
            <input id="email" class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                   type="email" name="email" value="{{ old('email') }}"
                   required autofocus autocomplete="username" placeholder="you@example.com">
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px">
                <label class="form-label" for="password" style="margin-bottom:0">Password</label>
                @if (Route::has('password.request'))
                    <a class="auth-link" href="{{ route('password.request') }}">Forgot password?</a>
                @endif
            </div>
            <input id="password" class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                   type="password" name="password"
                   required autocomplete="current-password" placeholder="••••••••">
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check">
            <input id="remember_me" type="checkbox" name="remember">
            <label for="remember_me">Remember me for 30 days</label>
        </div>

        <button type="submit" class="btn-primary">Sign in</button>
    </form>

    <div class="auth-footer">
        Don't have an account? <a href="{{ route('register') }}">Create one</a>
    </div>
</x-guest-layout>
