<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                @php
                    $moduleLinks = [];
                    if (Auth::check()) {
                        $moduleLinks = \Illuminate\Support\Facades\DB::table('modules')
                            ->join('role_modules', 'role_modules.module_id', '=', 'modules.id')
                            ->where('role_modules.role_id', Auth::user()->role_id)
                            ->orderBy('modules.weight')
                            ->orderBy('modules.name')
                            ->select('modules.name', 'modules.slug')
                            ->get();
                    }
                @endphp
                <div class="hidden space-x-4 sm:-my-px sm:ms-10 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @foreach($moduleLinks as $module)
                        @php
                            $link = $module->slug ? url($module->slug) : '#';
                            $active = $module->slug ? request()->is(trim($module->slug, '/').'*') : false;
                        @endphp
                        <x-nav-link :href="$link" :active="$active">
                            {{ $module->name }}
                        </x-nav-link>
                    @endforeach
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4" x-data="timerBadge()" x-init="init()">
                <a x-cloak x-show="running" href="{{ route('timer.index') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold border border-indigo-100 hover:bg-indigo-100">
                    <span class="text-[11px] uppercase tracking-wide">Timer</span>
                    <span class="font-mono text-sm" x-text="formatted"></span>
                </a>

                @php
                    $navUser = Auth::user();
                    $navImg = $navUser?->image_url
                        ? (str_contains($navUser->image_url, '/') ? $navUser->image_url : 'files/users/'.$navUser->image_url)
                        : null;
                    $navInitials = collect(explode(' ', trim($navUser?->name ?? '')))
                        ->filter()
                        ->map(fn ($part) => mb_substr($part, 0, 1))
                        ->take(2)
                        ->implode('');
                @endphp

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-3 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:text-gray-900 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 p-0.5 shadow-lg">
                                    <div class="h-full w-full rounded-full overflow-hidden bg-white flex items-center justify-center text-xs font-semibold text-gray-600">
                                        @if($navImg)
                                            <img src="{{ asset('storage/'.$navImg) }}" alt="{{ $navUser?->name }}" class="h-full w-full object-cover">
                                        @else
                                            {{ $navInitials ?: '?' }}
                                        @endif
                                    </div>
                                </div>
                                <div class="text-left">
                                    <div class="font-semibold text-gray-900">{{ $navUser?->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $navUser?->email }}</div>
                                </div>
                            </div>
                            <svg class="fill-current h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @foreach($moduleLinks as $module)
                @php
                    $link = $module->slug ? url($module->slug) : '#';
                    $active = $module->slug ? request()->is(trim($module->slug, '/').'*') : false;
                @endphp
                <x-responsive-nav-link :href="$link" :active="$active">
                    {{ $module->name }}
                </x-responsive-nav-link>
            @endforeach
            <div class="px-4" x-data="timerBadge()" x-init="init()">
                <a x-cloak x-show="running" href="{{ route('timer.index') }}" class="mt-2 inline-flex items-center gap-2 px-3 py-2 rounded-md bg-indigo-50 text-indigo-700 text-xs font-semibold border border-indigo-100 hover:bg-indigo-100">
                    <span class="text-[11px] uppercase tracking-wide">Timer</span>
                    <span class="font-mono text-sm" x-text="formatted"></span>
                </a>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    function timerBadge() {
        return {
            running: false,
            elapsedBaseline: 0,
            sampledAt: null,
            elapsed: 0,
            maxSeconds: 7200,
            tickId: null,
            refreshId: null,
            init() {
                this.refresh();
                this.tickId = setInterval(() => this.tick(), 1000);
                this.refreshId = setInterval(() => this.refresh(), 20000);
            },
            async refresh() {
                try {
                    const response = await fetch('{{ route('timer.status') }}', {
                        headers: { Accept: 'application/json' },
                    });
                    if (!response.ok) {
                        return;
                    }
                    const data = await response.json();
                    this.applyState(data);
                } catch (e) {
                    console.error(e);
                }
            },
            applyState(data) {
                this.maxSeconds = typeof data?.max_seconds === 'number' ? data.max_seconds : this.maxSeconds;
                this.running = Boolean(data?.running);
                this.elapsedBaseline = Number(data?.elapsed ?? 0);
                this.elapsed = this.elapsedBaseline;
                this.sampledAt = this.running ? Date.now() : null;
                if (!this.running) {
                    this.elapsedBaseline = 0;
                }
            },
            tick() {
                if (!this.running || !this.sampledAt) {
                    return;
                }
                const diff = Math.floor((Date.now() - this.sampledAt) / 1000);
                this.elapsed = Math.min(this.elapsedBaseline + diff, this.maxSeconds);
                if (this.elapsed >= this.maxSeconds) {
                    this.running = false;
                }
            },
            formatSeconds(value) {
                const secs = Math.max(0, Math.floor(value));
                const hrs = Math.floor(secs / 3600);
                const mins = Math.floor((secs % 3600) / 60);
                const rest = secs % 60;
                return `${String(hrs).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(rest).padStart(2, '0')}`;
            },
            get formatted() {
                return this.formatSeconds(this.elapsed);
            },
        };
    }
</script>
