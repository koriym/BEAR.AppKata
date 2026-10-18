{{ extends ('layout/Admin/page-fullscreen') }}

{{ setBlock ('head_scripts') }}
{{ parentBlock () }}
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<script>
  function cfTurnstileChecked() {
    document.getElementById('continue').removeAttribute('disabled');
  }
  function cfTurnstileExpired() {
    document.getElementById('continue').setAttribute('disabled', 'disabled');
  }
  function cfTurnstileError() {
    document.getElementById('continue').setAttribute('disabled', 'disabled');
  }
  function cfTurnstileTimeout() {
    document.getElementById('continue').setAttribute('disabled', 'disabled');
  }
</script>
{{ endBlock () }}

{{ setBlock ('body') }}
<div class="h-[100svh] grid place-content-center">
    <div class="relative w-96 h-min p-8 rounded-xl bg-white shadow-[0_1px_3px_rgba(15,23,42,0.03),0_1px_2px_rgba(15,23,42,0.06)] ring-1 ring-slate-600/[0.04]">
        <h2 class="text-xl text-center tracking-widest font-sans font-bold">Forgot password</h2>

        {{= form() }}
            {{ if (isset($recaptchaError) && $recaptchaError): }}
            {{= render('partials/Admin/AlertError', ['text' => 'CAPTCHA error']) }}
            {{ endif }}
            <div class="mt-5">
                <label class="block mt-5">
                    <span class="block text-sm font-sans font-normal text-slate-700 select-none">Email address</span>
                    {{= adminText(form: $form, input: 'emailAddress') }}
                    {{= adminFormError(form: $form, input: 'emailAddress') }}
                </label>
                <div class="flex justify-center mt-5">
                    {{= cfTurnstileWidget(cloudflareTurnstileSiteKey: $cloudflareTurnstileSiteKey, action: 'login', checked: 'cfTurnstileChecked', expired: 'cfTurnstileExpired', error: 'cfTurnstileError', timeout: 'cfTurnstileTimeout') }}
                </div>
                <label class="block mt-5 text-center">
                    {{= adminSubmit(form: $form, input: 'continue', attribs: ['id' => 'continue', 'value' => 'CONTINUE', 'disabled' => 'disabled', 'data-submit-once' => '1']) }}
                </label>
            </div>
        </form>
        <div class="absolute top-full right-0 w-full h-px rounded-full max-w-sm bg-gradient-to-r from-transparent from-10% via-purple-500 to-transparent drop-shadow-xl"></div>
    </div>
    <p class="text-sm text-center mt-2"><a href="{{= url('/admin/login') }}">Already have an account?</a></p>
</div>
{{ endBlock () }}
