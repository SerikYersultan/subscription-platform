<x-guest-layout>
    <div class="auth-title">Create your account</div>
    <div class="auth-sub">Start tracking your subscriptions for free</div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="name">Full name</label>
            <input id="name" class="form-input {{ $errors->has('name') ? 'error' : '' }}"
                   type="text" name="name" value="{{ old('name') }}"
                   required autofocus autocomplete="name" placeholder="James Doe">
            @error('name')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email address</label>
            <input id="email" class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                   type="email" name="email" value="{{ old('email') }}"
                   required autocomplete="username" placeholder="you@example.com">
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input id="password" class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                   type="password" name="password"
                   required autocomplete="new-password" placeholder="Min. 8 characters">
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group" style="margin-bottom:20px">
            <label class="form-label" for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" class="form-input"
                   type="password" name="password_confirmation"
                   required autocomplete="new-password" placeholder="••••••••">
        </div>

        <button type="submit" class="btn-primary">Create account</button>
    </form>

    <div class="auth-footer">
        Already have an account? <a href="{{ route('login') }}">Sign in</a>
    </div>
</x-guest-layout>
