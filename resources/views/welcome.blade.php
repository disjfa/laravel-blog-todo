<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel News API') }}</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-indigo-500/20 via-slate-950 to-slate-950"></div>

        <header class="relative mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-6">
            <div class="text-lg font-semibold tracking-wide">{{ config('app.name', 'Laravel News API') }}</div>
            @auth
                <div class="flex items-center gap-2">
                    <a href="{{ url('/admin') }}" class="rounded-md border border-slate-700 bg-slate-900 px-4 py-2 text-sm font-medium hover:bg-slate-800">
                        Account
                    </a>
                    <form method="POST" action="{{ route('filament.admin.auth.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="rounded-md border border-slate-700 bg-slate-900 px-4 py-2 text-sm font-medium hover:bg-slate-800">
                            Logout
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ url('/admin/login') }}" class="rounded-md border border-slate-700 bg-slate-900 px-4 py-2 text-sm font-medium hover:bg-slate-800">
                    Login
                </a>
            @endauth
        </header>

        <main class="relative mx-auto w-full max-w-6xl px-6 pb-16 pt-10 md:pt-16">
            <section class="grid gap-12 lg:grid-cols-2 lg:items-center">
                <div>
                    <p class="mb-4 inline-block rounded-full border border-indigo-400/30 bg-indigo-500/10 px-3 py-1 text-xs font-medium text-indigo-200">
                        Customer-scoped content operations
                    </p>
                    <h1 class="text-4xl font-bold leading-tight text-white md:text-5xl">
                        We help teams publish blogs and execute social tasks faster.
                    </h1>
                    <p class="mt-5 max-w-xl text-base leading-7 text-slate-300 md:text-lg">
                        {{ config('app.name', 'Laravel News API') }} combines a customer-scoped Blog CMS, Todo Kanban, and automation engine so your team can plan, publish, and track work in one secure workflow.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        @auth
                            <a href="{{ url('/admin') }}" class="rounded-md bg-indigo-500 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                                Account
                            </a>
                        @else
                            <a href="{{ url('/admin/login') }}" class="rounded-md bg-indigo-500 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                                Login
                            </a>
                        @endauth
                        <a href="{{ url('/api/docs') }}" class="rounded-md border border-slate-700 bg-slate-900 px-5 py-3 text-sm font-semibold text-slate-100 hover:bg-slate-800">
                            View API Docs
                        </a>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-2xl shadow-indigo-900/20">
                    <h2 class="text-xl font-semibold text-white">What we do</h2>
                    <ul class="mt-4 space-y-4 text-sm leading-6 text-slate-300">
                        <li><span class="font-semibold text-slate-100">Customer isolation:</span> Every blog, todo, asset, and automation action is scoped per customer.</li>
                        <li><span class="font-semibold text-slate-100">Content to execution:</span> Creating a blog can trigger social todo generation with due-date rules.</li>
                        <li><span class="font-semibold text-slate-100">Remote assets:</span> Upload and manage media through S3, FTP, or Cloudinary connections.</li>
                        <li><span class="font-semibold text-slate-100">Team visibility:</span> Use a Kanban workflow to track status from todo to done.</li>
                    </ul>
                </div>
            </section>

            <section class="mt-14 grid gap-4 md:grid-cols-3">
                <article class="rounded-xl border border-slate-800 bg-slate-900/60 p-5">
                    <h3 class="font-semibold text-white">Secure by design</h3>
                    <p class="mt-2 text-sm text-slate-300">Policy-based access and tenant-aware architecture prevent cross-customer data access.</p>
                </article>
                <article class="rounded-xl border border-slate-800 bg-slate-900/60 p-5">
                    <h3 class="font-semibold text-white">Automation built in</h3>
                    <p class="mt-2 text-sm text-slate-300">Reusable templates transform published content into structured social tasks.</p>
                </article>
                <article class="rounded-xl border border-slate-800 bg-slate-900/60 p-5">
                    <h3 class="font-semibold text-white">API + Admin parity</h3>
                    <p class="mt-2 text-sm text-slate-300">Shared requests, policies, and query patterns keep behavior consistent across surfaces.</p>
                </article>
            </section>
        </main>
    </div>
</body>
</html>
