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

            <!-- Live password strength indicator (always visible) -->
            <div id="pw-checker" style="display:block; margin-top:10px; padding:10px 12px; background:#f8f9fa; border-radius:8px; border:1px solid #e5e7eb;">
                <div style="font-size:11px; font-weight:600; color:#374151; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.04em;">Password strength</div>

                <!-- Progress bar -->
                <div style="height:4px; background:#e5e7eb; border-radius:4px; margin-bottom:8px; overflow:hidden;">
                    <div id="pw-bar" style="height:100%; width:0%; border-radius:4px; transition:all 0.3s ease;"></div>
                </div>

                <div id="pw-req-length"  class="pw-req" style="font-size:11px; color:#9ca3af; display:flex; align-items:center; gap:5px; margin-bottom:3px;">
                    <span class="pw-dot">○</span> At least 8 characters
                </div>
                <div style="font-size:10px; color:#b0b5be; margin:5px 0 3px; font-weight:600; text-transform:uppercase; letter-spacing:0.04em;">Need 3 or more of:</div>
                <div id="pw-req-upper"  class="pw-req" style="font-size:11px; color:#9ca3af; display:flex; align-items:center; gap:5px; margin-bottom:3px;">
                    <span class="pw-dot">○</span> Uppercase letters (A–Z)
                </div>
                <div id="pw-req-lower"  class="pw-req" style="font-size:11px; color:#9ca3af; display:flex; align-items:center; gap:5px; margin-bottom:3px;">
                    <span class="pw-dot">○</span> Lowercase letters (a–z)
                </div>
                <div id="pw-req-digit"  class="pw-req" style="font-size:11px; color:#9ca3af; display:flex; align-items:center; gap:5px; margin-bottom:3px;">
                    <span class="pw-dot">○</span> Numbers (0–9)
                </div>
                <div id="pw-req-symbol" class="pw-req" style="font-size:11px; color:#9ca3af; display:flex; align-items:center; gap:5px;">
                    <span class="pw-dot">○</span> Special symbols (!, $, #, %)
                </div>
            </div>
        </div>

        <script>
            (function () {
                var input   = document.getElementById('password');
                var checker = document.getElementById('pw-checker');
                var bar     = document.getElementById('pw-bar');

                function setReq(id, ok) {
                    var el  = document.getElementById(id);
                    var dot = el.querySelector('.pw-dot');
                    el.style.color  = ok ? '#16a34a' : '#9ca3af';
                    dot.textContent = ok ? '●' : '○';
                    return ok ? 1 : 0;
                }

                input.addEventListener('input', function () {
                    var v = this.value;

                    setReq('pw-req-length', v.length >= 8);

                    var score = 0;
                    score += setReq('pw-req-upper',  /[A-Z]/.test(v));
                    score += setReq('pw-req-lower',  /[a-z]/.test(v));
                    score += setReq('pw-req-digit',  /[0-9]/.test(v));
                    score += setReq('pw-req-symbol', /[^A-Za-z0-9]/.test(v));

                    var lengthOk = v.length >= 8;
                    var colors   = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
                    var widths   = ['25%', '50%', '75%', '100%'];

                    if (!lengthOk) {
                        bar.style.width      = '10%';
                        bar.style.background = '#ef4444';
                    } else {
                        bar.style.width      = widths[Math.min(score, 4) - 1] || '10%';
                        bar.style.background = score >= 3 ? colors[Math.min(score, 4) - 1] : colors[score - 1] || '#ef4444';
                    }
                });
            })();
        </script>

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
