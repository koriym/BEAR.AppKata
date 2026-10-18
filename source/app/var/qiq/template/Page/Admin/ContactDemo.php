{{ extends ('layout/Admin/page') }}

{{ setBlock ('title') }}Contact Demo | {{ parentBlock () }}{{ endBlock () }}

{{ setBlock ('body_content') }}
{{= form() }}
    {{ if ($form->mode == $confirmStep->name): }}
    <div class="mt-5">
        <label class="block">
            <span class="block text-sm font-thin text-slate-500 tracking-wide">Username</span>
            {{= $form->username }}
            {{= adminHidden(form: $form, input: 'username') }}
        </label>
        <label class="block mt-5 text-center">
            {{= adminSubmit(form: $form, input: 'mode', attribs: ['value' => $inputStep->name, 'data-submit-once' => '1']) }}
            {{= adminSubmit(form: $form, input: 'mode', attribs: ['value' => $completeStep->name, 'data-submit-once' => '1']) }}
        </label>
    </div>
    {{ else: }}
    <div class="mt-5">
        <label class="block">
            <span class="block text-sm font-thin text-slate-500 tracking-wide">Username</span>
            {{= adminText(form: $form, input: 'username') }}
            {{= adminFormError(form: $form, input: 'username') }}
        </label>
        <label class="block mt-5 text-center">
            {{= adminSubmit(form: $form, input: 'mode', attribs: ['value' => $confirmMode->name, 'data-submit-once' => '1']) }}
        </label>
    </div>
    {{ endif }}
</form>
{{ endBlock () }}
