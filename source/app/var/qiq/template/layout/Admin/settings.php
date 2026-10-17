{{ extends ('layout/Admin/base') }}

{{ setBlock ('head') }}
{{ setBlock ('head_meta') }}{{= getBlock () ~}}
{{ setBlock ('head_styles') }}
<link href="https://fonts.googleapis.com/css?family=Noto+Sans+JP:100|Noto+Sans+JP:400|Noto+Sans+JP:700|Noto+Sans+JP:900|Roboto:100|Roboto:400|Roboto:700|Roboto:900&display=swap&subset=japanese" rel="stylesheet">
<link href="/admin/css/bundle.css" rel="stylesheet">
<link href="/admin/css/tailwind.css" rel="stylesheet">
{{= getBlock () ~}}
{{ setBlock ('head_scripts') }}
<script src="/admin/js/bundle.min.js"></script>
{{= getBlock () ~}}
{{ endBlock () }}

{{ setBlock ('body') }}
<header class="sticky top-0 z-40 w-full backdrop-blur flex-none transition-colors duration-500 lg:z-50 lg:border-b lg:border-slate-900/10 bg-white/70 supports-backdrop-blur:bg-white/60">
    {{ setBlock ('body_header') }}
    <div class="max-w-7xl mx-auto">
        <div class="py-4 border-b border-slate-900/10 px-4 lg:px-8 lg:border-0">
            <div class="relative flex items-center">
                <a class="mr-3 flex-none overflow-hidden lg:w-auto font-sans font-black text-lg bg-clip-text text-transparent bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500" href="/admin/index">
                    ADMINISTRATOR
                </a>
                <div class="relative hidden lg:flex items-center ml-auto">
                    <div class="flex items-center border-l border-slate-200 ml-6 pl-6 dark:border-slate-800">
                        {{= form(action: '/admin/logout') }}
                            <button>
                                {{= render('partials/Admin/InlineIcon', ['name' => 'arrow-left-on-rectangle']) }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{= getBlock () ~}}
</header>
<div class="max-w-7xl mx-auto px-5 lg:px-8 pt-5 flex flex-row">
    {{= render('partials/Admin/InlineIcon', ['name' => 'user-circle']) }}
    <p class="text-base lg:text-xl font-extrabold ml-2">{{h $context->displayName() }}</p>
</div>
<div class="max-w-7xl mx-auto px-0 lg:px-8 pt-5 flex flex-row grow">
    {{ $isNavVisible = isset($isNavVisible) ? $isNavVisible : false }}
    {{ if ($isNavVisible): }}
    <div class="hidden lg:block lg:w-72 lg:overflow-y-auto">
        <nav class="block leading-6 relative">
            {{ setBlock ('body_nav') }}
            {{ $viewName = $this->getView() }}
            {{ $isEmailPage = $viewName === 'Page/Admin/Settings/Emails' }}
            {{ $isPasswordPage = $viewName === 'Page/Admin/Settings/Password' }}
            {{ $isDeletePage = $viewName === 'Page/Admin/Settings/Delete' }}
            <ul>
                <li>
                    <h5 class="font-sans font-bold text-base tracking-wide mb-8 lg:mb-3">Settings</h5>
                    <ul class="space-y-6 lg:space-y-2 border-l border-slate-200">
                        <li>
                            <a href="/admin/settings/emails" class="text-base tracking-wide pl-4 -ml-px border-l {{ if ($isEmailPage): }}font-bold text-lime-500 border-current{{ else: }}border-transparent hover:border-slate-400{{ endif }}">
                                Emails
                            </a>
                        </li>
                        <li>
                            <a href="/admin/settings/password" class="text-base tracking-wide pl-4 -ml-px border-l {{ if ($isPasswordPage): }}font-bold text-lime-500 border-current{{ else: }}border-transparent hover:border-slate-400{{ endif }}">
                                Password
                            </a>
                        </li>
                        <li>
                            <a href="/admin/settings/delete" class="text-base tracking-wide pl-4 -ml-px border-l {{ if ($isDeletePage): }}font-bold text-lime-500 border-current{{ else: }}border-transparent hover:border-slate-400{{ endif }}">
                                Delete
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            {{= getBlock () ~}}
        </nav>
    </div>
    {{ endif }}
    <div class="block lg:flex lg:flex-col lg:grow w-full">
        <div class="mx-5 lg:mx-0 lg:grow whitespace-normal break-words">
            <main>{{ setBlock ('body_content') }}{{= getBlock () ~}}</main>
        </div>
        <div class="mx-5 lg:mx-0 my-10">
            <footer>{{ setBlock ('body_footer') }}<p class="font-sans font-thin text-sm">Copyright &copy; 2023 apple-x-co inc.</p>{{= getBlock () ~}}</footer>
        </div>
    </div>
</div>
{{ endBlock () }}
