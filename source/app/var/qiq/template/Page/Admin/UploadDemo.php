{{ extends ('layout/Admin/page') }}

{{ setBlock ('title') }}Upload Demo | {{ parentBlock () }}{{ endBlock () }}

{{ setBlock ('body_content') }}
<form method="post" enctype="multipart/form-data">
    <div class="mt-5">
        <label class="block">
            <span class="block text-sm font-thin text-slate-500 tracking-wide">Image file</span>
            {{= adminFile(form: $form, input: 'file') }}
            {{= adminFormError(form: $form, input: 'file') }}
        </label>
        {{ if (isset($fileUpload)): }}
        <div class="block mt-5">
            <p class="font-bold">Koriym\FileUpload\FileUpload</p>
            <p>name: {{h $fileUpload->name }}</p>
            <p>type: {{h $fileUpload->type }}</p>
            <p>size: {{= $fileUpload->size }}</p>
            <p>tmpName: {{= $fileUpload->tmpName }}</p>
            <p>extension: {{= $fileUpload->extension }}</p>
        </div>
        {{ endif }}
        {{ if (isset($errorFileUpload)): }}
        <div class="block mt-5">
            <p class="font-bold">Koriym\FileUpload\ErrorFileUpload</p>
            <p>message: {{h $errorFileUpload->message }}</p>
        </div>
        {{ endif }}
        <label class="block mt-5 text-center">
            {{= adminSubmit(form: $form, input: 'submit', attribs: ['value' => 'UPLOAD', 'data-submit-once' => '1']) }}
            {{= csrfTokenField(form: $form) }}
        </label>
    </div>
</form>
{{ endBlock () }}
