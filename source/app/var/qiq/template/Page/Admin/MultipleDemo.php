{{ extends ('layout/Admin/page') }}

{{ setBlock ('title') }}Multiple Demo | {{ parentBlock () }}{{ endBlock () }}

{{ setBlock ('body_content') }}
{{= form() }}
    <div class="mt-5">
        <label class="block">
            <span class="block text-sm font-sans font-normal text-slate-700">Fruits</span>
            {{= adminCheckBox(form: $form, input: 'fruits') }}
            {{= adminFormError(form: $form, input: 'fruits') }}
        </label>
        <label class="block mt-5">
            <span class="block text-sm font-sans font-normal text-slate-700">Primary languages</span>
            {{= adminSelect(form: $form, input: 'primaryLanguage') }}
            {{= adminFormError(form: $form, input: 'primaryLanguage') }}
        </label>
        <label class="block mt-5">
            <span class="block text-sm font-sans font-normal text-slate-700">Available Languages</span>
            {{= adminSelect(form: $form, input: 'languages') }}
            {{= adminFormError(form: $form, input: 'languages') }}
        </label>
        <label class="block mt-5">
            <span class="block text-sm font-sans font-normal text-slate-700">Programming Languages</span>
            {{= adminSelect(form: $form, input: 'programmingLanguages') }}
            {{= adminFormError(form: $form, input: 'programmingLanguages') }}
        </label>
        <label class="block mt-5 text-center">
            {{= adminSubmit(form: $form, input: 'submit', attribs: ['value' => 'SEND', 'data-submit-once' => '1']) }}
        </label>
    </div>
</form>
{{ endBlock () }}
