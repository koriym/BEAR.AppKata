{{ extends ('layout/Admin/settings') }}

{{ $this->addData(['isNavVisible' => true]) }}
{{ setBlock ('body_content') }}
{{= render('partials/Admin/Heading1', ['title' => 'Emails']) }}

{{ $adminEmails = $admin->emails }}
{{ if (! empty($adminEmails)): }}
<div class="border border-slate-300 rounded-2xl mt-5">
    <ul>
        {{ foreach ($adminEmails as $index0 => $adminEmail): }}
        <li class="flex gap-2 p-4 {{ if ($index0 > 0): }}border-t border-slate-300{{ endif }}">
            <div class="flex-1 col-span-3">
                <p class="text-sm lg:text-base font-bold">{{h $adminEmail->emailAddress }}</p>
                {{ if ($adminEmail->verifiedDate === null): }}
                <p class="text-xs lg:text-sm font-thin">未確認</p>
                {{ else: }}
                <p class="text-xs lg:text-sm font-thin">確認済 {{h $adminEmail->verifiedDate->format('Y-m-d H:i:s') }}</p>
                {{ endif }}
            </div>
            <div class="flex-none text-right">
                {{= form(action: url('/admin/settings/emails/delete', ['id' => $adminEmail->id])) }}
                    <button type="submit" value="削除" class="text-rose-500 focus:outline-none">
                        {{= render('partials/Admin/InlineIcon', ['name' => 'trash']) }}
                    </button>
                </form>
            </div>
        </li>
        {{ endforeach }}
    </ul>
</div>
{{ endif }}

{{= form() }}
    <label class="block mt-5">
        <span class="block text-sm font-sans font-normal text-slate-700 select-none">Email</span>
        {{= adminText(form: $form, input: 'emailAddress') }}
        {{= adminFormError(form: $form, input: 'emailAddress') }}
    </label>
    <label class="block mt-5 text-center">
        {{= adminSubmit(form: $form, input: 'create', attribs: ['value' => 'Create', 'data-submit-once' => '1']) }}
    </label>
</form>
{{ endBlock () }}
