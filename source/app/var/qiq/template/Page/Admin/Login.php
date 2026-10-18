{{ extends ('layout/Admin/page-fullscreen') }}

{{ setBlock ('head_scripts') }}
{{ parentBlock () }}
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<script>
  function cfTurnstileChecked() {
    document.getElementById('login').removeAttribute('disabled');
  }
  function cfTurnstileExpired() {
    document.getElementById('login').setAttribute('disabled', 'disabled');
  }
  function cfTurnstileError() {
    document.getElementById('login').setAttribute('disabled', 'disabled');
  }
  function cfTurnstileTimeout() {
    document.getElementById('login').setAttribute('disabled', 'disabled');
  }
</script>
{{ endBlock () }}

{{ setBlock ('body') }}
<div class="h-[100svh] grid place-content-center">
    <div class="relative w-96 h-min p-8 rounded-xl bg-white shadow-[0_1px_3px_rgba(15,23,42,0.03),0_1px_2px_rgba(15,23,42,0.06)] ring-1 ring-slate-600/[0.04]">
        <div class="flex justify-center">
            <h2 class="text-xl text-center tracking-widest font-black bg-clip-text text-transparent bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500">Bear App</h2>
        </div>

        {{= form(name: 'login_form') }}
            {{ if (isset($authError) && $authError): }}
                {{= render('partials/Admin/AlertError', ['text' => 'Authentication error']) }}
            {{ endif }}
            {{ if (isset($captchaError) && $captchaError): }}
                {{= render('partials/Admin/AlertError', ['text' => 'CAPTCHA error']) }}
            {{ endif }}
            <div class="mt-5">
                <label class="block">
                    <span class="block text-sm font-sans font-normal text-slate-700 select-none">Username</span>
                    {{= adminText(form: $form, input: 'username') }}
                    {{= adminFormError(form: $form, input: 'username') }}
                </label>
                <label class="block mt-5">
                    <span class="block text-sm font-sans font-normal text-slate-700 select-none">Password</span>
                    {{= adminText(form: $form, input: 'password') }}
                    {{= adminFormError(form: $form, input: 'password') }}
                </label>
                <p class="text-sm text-right mt-1"><a href="{{= url('/admin/forgot-password') }}">Forgot password?</a></p>
                <label class="block mt-5">
                    <span class="block top-2 left-3 text-sm font-normal text-slate-700 tracking-wide select-none">Remember</span>
                    {{= adminCheckBox(form: $form, input: 'remember') }}
                    {{= adminFormError(form: $form, input: 'remember') }}
                </label>
                <div class="flex justify-center mt-5">
                    {{= cfTurnstileWidget(cloudflareTurnstileSiteKey: $cloudflareTurnstileSiteKey, action: 'login', checked: 'cfTurnstileChecked', expired: 'cfTurnstileExpired', error: 'cfTurnstileError', timeout: 'cfTurnstileTimeout') }}
                </div>
                <label class="block mt-5 text-center">
                    {{= adminSubmit(form: $form, input: 'login', attribs: ['id' => 'login', 'value' => 'LOGIN', 'disabled' => 'disabled', 'data-submit-once' => '1']) }}
                </label>
            </div>
        </form>
        <div class="absolute top-full right-0 w-full h-px rounded-full max-w-sm bg-gradient-to-r from-transparent from-10% via-purple-500 to-transparent drop-shadow-xl"></div>
    </div>
    <p class="text-sm text-center mt-2"><a href="{{= url('/admin/join') }}">Create an account</a></p>
</div>
{{ endBlock () }}
