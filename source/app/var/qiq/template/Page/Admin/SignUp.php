{{ extends ('layout/Admin/base') }}

{{ setBlock ('body') }}
<div class="h-[100svh] grid place-content-center">
    <div class="relative w-80 md:w-96 h-min p-5 md:p-8 rounded-xl bg-white shadow-[0_1px_3px_rgba(15,23,42,0.03),0_1px_2px_rgba(15,23,42,0.06)] ring-1 ring-slate-600/[0.04]">
        <h2 class="text-xl text-center tracking-widest font-sans font-bold">Sign-Up</h2>

        {{= form() }}
            <div class="mt-5">
                <label class="block mt-5">
                    <span class="block text-sm font-sans font-normal text-slate-700 select-none">Display name</span>
                    {{= adminText(form: $form, input: 'displayName') }}
                    {{= adminFormError(form: $form, input: 'displayName') }}
                </label>
                <label class="block mt-5">
                    <span class="block text-sm font-sans font-normal text-slate-700 select-none">Username</span>
                    {{= adminText(form: $form, input: 'username') }}
                    {{= adminFormError(form: $form, input: 'username') }}
                </label>
                <label class="block mt-5">
                    <span class="block text-sm font-sans font-normal text-slate-700 select-none">Password</span>
                    {{= adminText(form: $form, input: 'password') }}
                    {{= adminFormError(form: $form, input: 'password') }}
                </label>
                <label class="block mt-5 text-center">
                    {{= adminHidden(form: $form, input: 'signature') }}
                    {{= adminFormError(form: $form, input: 'signature') }}
                    {{= adminSubmit(form: $form, input: 'continue', attribs: ['value' => 'CONTINUE', 'data-submit-once' => '1']) }}
                </label>
            </div>
        </form>
        <div class="absolute top-full right-0 w-full h-px rounded-full max-w-sm bg-gradient-to-r from-transparent from-10% via-purple-500 to-transparent drop-shadow-xl"></div>
    </div>
</div>
{{ endBlock () }}
