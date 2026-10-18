{{ extends ('layout/Admin/page') }}

{{ setBlock ('title') }}Fieldset Demo | {{ parentBlock () }}{{ endBlock () }}

{{ setBlock ('body_content') }}
{{= form() }}
    <div class="mt-5">
        <div class="mt-5">
            <h2 class="inline-block text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight">Home</h2>
            {{= adminFormError(form: $form, input: 'home') }}
            {{ $home = $form->home }}
            <label class="block mt-5">
                <span class="block text-sm font-sans font-normal text-slate-700">Zip</span>
                {{= adminText(form: $form, input: 'zip', fieldset: $home) }}
                {{= adminFieldsetError(fieldset: $home, input: 'zip') }}
            </label>
            <label class="block mt-5">
                <span class="block text-sm font-sans font-normal text-slate-700">State</span>
                {{= adminText(form: $form, input: 'state', fieldset: $home) }}
                {{= adminFieldsetError(fieldset: $home, input: 'state') }}
            </label>
            <label class="block mt-5">
                <span class="block text-sm font-sans font-normal text-slate-700">City</span>
                {{= adminText(form: $form, input: 'city', fieldset: $home) }}
                {{= adminFieldsetError(fieldset: $home, input: 'city') }}
            </label>
            <label class="block mt-5">
                <span class="block text-sm font-sans font-normal text-slate-700">Street</span>
                {{= adminText(form: $form, input: 'street', fieldset: $home) }}
                {{= adminFieldsetError(fieldset: $home, input: 'street') }}
            </label>
            <label class="block mt-5">
                <span class="block text-sm font-sans font-normal text-slate-700">House type</span>
                {{= adminRadio(form: $form, input: 'houseType', fieldset: $home) }}
                {{= adminFieldsetError(fieldset: $home, input: 'houseType') }}
            </label>
            <label class="block mt-5">
                <span class="block text-sm font-sans font-normal text-slate-700">Smartphone you own</span>
                {{= adminRadio(form: $form, input: 'smartphones', fieldset: $home) }}
                {{= adminFieldsetError(fieldset: $home, input: 'smartphones') }}
            </label>
        </div>

        <div class="mt-5">
            <h2 class="inline-block text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight">Deliveries</h2>
            {{= adminFormError(form: $form, input: 'deliveries') }}
            {{ foreach ($form->deliveries as $index => $delivery):  }}
            <div class="mt-5">
                <h2 class="inline-block text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight">delivery{{= $index + 1 }}</h2>
                <label class="block mt-5">
                    <span class="block text-sm font-sans font-normal text-slate-700">Zip</span>
                    {{= adminText(form: $form, input: 'zip', fieldset: $delivery) }}
                    {{= adminFieldsetError(fieldset: $delivery, input: 'zip') }}
                </label>
                <label class="block mt-5">
                    <span class="block text-sm font-sans font-normal text-slate-700">State</span>
                    {{= adminText(form: $form, input: 'state', fieldset: $delivery) }}
                    {{= adminFieldsetError(fieldset: $delivery, input: 'state') }}
                </label>
                <label class="block mt-5">
                    <span class="block text-sm font-sans font-normal text-slate-700">City</span>
                    {{= adminText(form: $form, input: 'city', fieldset: $delivery) }}
                    {{= adminFieldsetError(fieldset: $delivery, input: 'city') }}
                </label>
                <label class="block mt-5">
                    <span class="block text-sm font-sans font-normal text-slate-700">Street</span>
                    {{= adminText(form: $form, input: 'street', fieldset: $delivery) }}
                    {{= adminFieldsetError(fieldset: $delivery, input: 'street') }}
                </label>
                <label class="block mt-5">
                    <span class="block text-sm font-sans font-normal text-slate-700">House type</span>
                    {{= adminRadio(form: $form, input: 'houseType', fieldset: $delivery) }}
                    {{= adminFieldsetError(fieldset: $delivery, input: 'houseType') }}
                </label>
                <label class="block mt-5">
                    <span class="block text-sm font-sans font-normal text-slate-700">Smartphone you own</span>
                    {{= adminRadio(form: $form, input: 'smartphones', fieldset: $delivery) }}
                    {{= adminFieldsetError(fieldset: $delivery, input: 'smartphones') }}
                </label>
            </div>
            {{ endforeach }}
        </div>

        <label class="block mt-20">
            <span class="block text-sm font-sans font-normal text-slate-700">Note</span>
            {{= adminText(form: $form, input: 'note') }}
            {{= adminFormError(form: $form, input: 'note') }}
        </label>
        <label class="block mt-5">
            <span class="block text-sm font-sans font-normal text-slate-700">Agree</span>
            {{= adminSelect(form: $form, input: 'agree') }}
            {{= adminFormError(form: $form, input: 'agree') }}
        </label>
        <label class="block mt-5 text-center">
            {{= adminSubmit(form: $form, input: 'submit', attribs: ['value' => 'SEND', 'data-submit-once' => '1']) }}
        </label>
    </div>
</form>
{{ endBlock () }}
