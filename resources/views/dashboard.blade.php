<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    {{--
    NOTE: We are NOT using <x-slot name="header"> here because we need the
        Tab Switcher buttons to share the 'x-data' scope with the content.
        The layout's default header slot is rendered outside of our x-data div,
        which breaks the interactivity. We recreate the header look below.
        --}}

        <div x-data="{
            tab: 'workspace',
            currentTime: '',
            greeting: '',
            greetingIcon: '',
            subtitle: '',
            init() {
                this.updateTime();
                setInterval(() => this.updateTime(), 1000);
            },
            updateTime() {
                const now = new Date();
                const hours = now.getHours();
                const mins = String(now.getMinutes()).padStart(2, '0');
                const secs = String(now.getSeconds()).padStart(2, '0');

                // Format: 07.53 am
                const ampm = hours >= 12 ? 'pm' : 'am';
                const h12 = hours % 12 || 12;
                this.currentTime = String(h12).padStart(2, '0') + '.' + mins + ' ' + ampm;

                if (hours >= 5 && hours < 12) {
                    this.greeting = '{{ __('Selamat Pagi') }}';
                    this.greetingIcon = '☀️';
                    this.subtitle = '{{ __('Semoga harimu menyenangkan! 🌟') }}';
                } else if (hours >= 12 && hours < 15) {
                    this.greeting = '{{ __('Selamat Siang') }}';
                    this.greetingIcon = '🌤️';
                    this.subtitle = '{{ __('Tetap semangat ya! 💪') }}';
                } else if (hours >= 15 && hours < 18) {
                    this.greeting = '{{ __('Selamat Sore') }}';
                    this.greetingIcon = '🌅';
                    this.subtitle = '{{ __('Sedikit lagi, kamu pasti bisa! ✨') }}';
                } else {
                    this.greeting = '{{ __('Selamat Malam') }}';
                    this.greetingIcon = '🌙';
                    this.subtitle = '{{ __('Istirahat yang cukup ya! 💤') }}';
                }
            }
        }" class="space-y-8">
            {{-- Clean White Header (Good Morning Style) --}}
            <div class="relative mb-8 -mt-4 overflow-hidden rounded-3xl border border-gray-200/80 bg-white/95 p-6 shadow-soft-lg ring-1 ring-black/5 backdrop-blur-sm sm:mx-0 sm:p-8">
                {{-- Subtle decorative background --}}
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-primary-50/60 to-transparent rounded-full -mr-20 -mt-20 pointer-events-none">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-40 h-40 bg-gradient-to-tr from-blue-50/40 to-transparent rounded-full -ml-16 -mb-16 pointer-events-none">
                </div>

                <div class="relative z-10 flex flex-col gap-7 sm:flex-row sm:items-start sm:justify-between sm:gap-6">
                    {{-- Left: Greeting + Clock --}}
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-2xl" x-text="greetingIcon">☀️</span>
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 font-display">
                                <span x-text="greeting">{{ __('Selamat Pagi') }}</span>, {{ auth()->user()->name }}!
                            </h1>
                        </div>
                        <p class="text-sm text-gray-400 ml-9" x-text="subtitle">{{ __('Semoga harimu menyenangkan! 🌟') }}</p>

                        {{-- Big Clock --}}
                        <div class="mt-4 ml-9">
                            <span class="text-4xl sm:text-5xl font-bold text-gray-900 font-display tracking-tight"
                                x-text="currentTime">07.53 am</span>
                        </div>
                    </div>

                    {{-- Right: Tabs + Button --}}
                    <div class="flex flex-col items-end gap-3 sm:mt-1">
                        {{-- Tab Switcher --}}
                        <div class="inline-flex w-fit rounded-2xl border border-gray-200 bg-gray-100/80 p-1 shadow-inner">
                            <button @click="tab = 'workspace'"
                                :class="{ 'bg-white shadow-md text-primary-700 font-semibold': tab === 'workspace', 'text-gray-500 hover:text-gray-700': tab !== 'workspace' }"
                                class="rounded-xl px-4 py-2.5 text-sm font-medium transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-primary-200 sm:px-5">
                                {{ __('Workspace') }}
                            </button>
                            <button @click="tab = 'tasks'"
                                :class="{ 'bg-white shadow-md text-primary-700 font-semibold': tab === 'tasks', 'text-gray-500 hover:text-gray-700': tab !== 'tasks' }"
                                class="rounded-xl px-4 py-2.5 text-sm font-medium transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-primary-200 sm:px-5">
                                {{ __('Tasks & Approval') }}
                            </button>
                        </div>

                        @if(auth()->user()->hasPermissionTo('create-document'))
                            <a href="{{ route('documents.create') }}"
                                class="hidden items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-300 hover:bg-primary-700 hover:shadow-lg hover:shadow-primary-500/25 sm:inline-flex">
                                <x-icon name="plus-circle" class="w-4 h-4" />
                                {{ __('Buat Pengajuan') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- TAB 1: WORKSPACE (HRIS) --}}
            <div x-show="tab === 'workspace'" x-data="{ deleteUrl: '' }"
                class="grid grid-cols-1 gap-6 animate-fade-in xl:grid-cols-4 xl:gap-8">
                {{-- Left Column: Profile & Quick Links --}}
                <div class="space-y-5 stagger-enter">
                    {{-- Profile Card (Enhanced) --}}
                    <div class="card-elevated group relative overflow-hidden rounded-3xl hover-lift shimmer-hover">
                        {{-- Decorative top gradient --}}
                        <div class="relative h-24 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900">
                            <div class="absolute inset-0 dot-pattern opacity-20"></div>
                        </div>

                        <div class="relative z-10 -mt-12 px-6 pb-7 text-center">
                            {{-- Avatar with gradient ring --}}
                            <div class="avatar-ring inline-block mx-auto mb-4 shadow-lg">
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                    class="w-20 h-20 object-cover">
                            </div>

                            <h3 class="text-lg font-bold text-gray-900">{{ auth()->user()->name }}</h3>
                            <p class="text-sm text-gray-500 mb-1">{{ auth()->user()->position ?? __('Karyawan') }}</p>

                            {{-- Stats mini cards --}}
                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <div class="stat-mini bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl">
                                    <span
                                        class="block text-2xl font-bold text-primary-700">{{ auth()->user()->annual_leave_balance }}</span>
                                    <span class="text-[11px] font-medium text-primary-600/70">{{ __('Sisa Cuti') }}</span>
                                </div>
                                <div class="stat-mini bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl">
                                    <span
                                        class="block text-2xl font-bold text-amber-700">{{ auth()->user()->sick_leave_used }}</span>
                                    <span class="text-[11px] font-medium text-amber-600/70">{{ __('Sakit (Hari)') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Links (Enhanced) --}}
                    <div class="card-elevated rounded-3xl p-5">
                        <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                            {{ __('Quick Links') }}
                        </h3>
                        <div class="space-y-1">
                            <a href="{{ route('profile.edit') }}" class="quick-link-card">
                                <div class="icon-wrap bg-blue-100">
                                    <x-icon name="user" class="w-5 h-5 text-blue-600" />
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700">{{ __('Edit Profil') }}</span>
                                    <p class="text-[11px] text-gray-400">{{ __('Perbarui data diri') }}</p>
                                </div>
                            </a>
                            @if(feature_active('module_hris'))
                            @if(auth()->user()->hasPermissionTo('menu-my-leaves'))
                            <a href="{{ route('leave-requests.create') }}" class="quick-link-card">
                                <div class="icon-wrap bg-green-100">
                                    <x-icon name="calendar" class="w-5 h-5 text-green-600" />
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700">{{ __('Request Cuti') }}</span>
                                    <p class="text-[11px] text-gray-400">{{ __('Ajukan permohonan') }}</p>
                                </div>
                            </a>
                            @endif
                            @endif
                        </div>
                    </div>

                    {{-- Who's Off Widget (Timeline Style) --}}
                    @if(feature_active('module_hris'))
                    <div class="card-elevated rounded-3xl p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                                {{ __('Who\'s Off Today') }}
                            </h3>
                            <span
                                class="text-[11px] font-medium text-gray-400 bg-gray-100 px-2 py-1 rounded-full">{{ now()->format('D, d M') }}</span>
                        </div>
                        <div class="space-y-0">
                            @forelse($whosOff as $leave)
                                <div class="timeline-item py-2.5">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $leave->user->avatar_url }}" alt="{{ $leave->user->name }}"
                                            class="w-8 h-8 rounded-full object-cover ring-2 ring-white shadow-sm">
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $leave->user->name }}
                                            </p>
                                            <p class="text-[11px] text-gray-400">{{ ucfirst($leave->type) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6">
                                    <div
                                        class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-2">
                                        <x-icon name="check-circle" class="w-6 h-6 text-green-400" />
                                    </div>
                                    <p class="text-sm text-gray-400">{{ __('Semua hadir 🎉') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Center Column: Announcements (Compact Feed) --}}
                <div class="space-y-4 xl:col-span-3 xl:space-y-5">
                    {{-- HR Banner (Only for HR) --}}
                    @if(auth()->user()->hasPermissionTo('menu-announcements'))
                        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-pink-500 to-rose-500 p-5 text-white shadow-soft-lg sm:p-6">
                            <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h2 class="text-lg font-bold">{{ __('Halo Tim HR') }}, {{ auth()->user()->name }}!</h2>
                                    <p class="mt-1 text-sm text-pink-100">{{ __('Kelola pengumuman dan informasi perusahaan di sini.') }}</p>
                                </div>
                                <x-ui.button href="{{ route('announcements.index') }}" variant="secondary" size="sm">
                                    {{ __('Kelola Pengumuman') }}
                                </x-ui.button>
                            </div>
                            <div class="pointer-events-none absolute bottom-0 right-0 translate-x-10 translate-y-10 opacity-10">
                                <x-icon name="megaphone" class="h-32 w-32" />
                            </div>
                        </div>
                    @endif

                    {{-- Compact announcement stream --}}
                    <div class="mx-auto max-w-3xl space-y-3">
                        <div class="flex items-center gap-3 px-1 pb-1">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-100">
                                <x-icon name="sparkles" class="h-4 w-4 text-primary-600" />
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('Feed Terbaru') }}</h3>
                                <p class="text-[11px] text-gray-400">{{ __('Pengumuman terbaru dan lampiran terkait') }}</p>
                            </div>
                            <div class="h-px flex-1 bg-gradient-to-r from-gray-200 to-transparent"></div>
                        </div>

                        @forelse($announcements as $announcement)
                            <article class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm transition-shadow hover:shadow-soft dark:border-gray-700 dark:bg-gray-800">
                                {{-- Compact card header --}}
                                <div class="flex items-center justify-between gap-3 px-4 py-3">
                                    <div class="flex min-w-0 items-center gap-2.5">
                                        <div class="relative shrink-0">
                                            <img src="{{ $announcement->creator->avatar_url }}"
                                                class="h-9 w-9 rounded-full border-2 border-gray-100 object-cover shadow-sm dark:border-gray-700"
                                                loading="lazy" decoding="async">
                                            @if($announcement->type === 'urgent' || $announcement->type === 'danger')
                                                <span class="absolute -bottom-0.5 -right-0.5 flex h-3 w-3">
                                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"></span>
                                                    <span class="relative inline-flex h-3 w-3 rounded-full border-2 border-white bg-red-500 dark:border-gray-800"></span>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="flex min-w-0 items-center gap-1.5 text-xs font-bold leading-tight text-gray-900 dark:text-white">
                                                <span class="truncate">{{ $announcement->creator->name }}</span>
                                                @if($announcement->type !== 'info')
                                                    <span class="shrink-0 rounded-full px-1.5 py-0.5 text-[9px] font-bold uppercase
                                                        {{ match ($announcement->type) {
                                                        'urgent' => 'bg-red-100 text-red-600',
                                                        'warning' => 'bg-yellow-100 text-yellow-600',
                                                        'danger' => 'bg-red-100 text-red-600',
                                                        default => 'bg-gray-100 text-gray-600'
                                                    } }}">
                                                        {{ $announcement->type }}
                                                    </span>
                                                @endif
                                            </h4>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400" title="{{ $announcement->created_at }}">
                                                {{ $announcement->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>

                                    @if(auth()->user()->hasPermissionTo('menu-announcements'))
                                        <div class="relative shrink-0" x-data="{ open: false }">
                                            <button @click="open = !open" @click.away="open = false"
                                                class="rounded-full p-1 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                                                <x-icon name="dots-horizontal" class="h-5 w-5" />
                                            </button>
                                            <div x-show="open" style="display: none;"
                                                class="absolute right-0 z-20 mt-2 w-48 rounded-xl border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                                <a href="{{ route('announcements.edit', $announcement) }}"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">{{ __('Edit Pengumuman') }}</a>
                                                <button
                                                    @click="deleteUrl = '{{ route('announcements.destroy', $announcement) }}'; open = false; $dispatch('open-modal', 'confirm-announcement-deletion')"
                                                    class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('Hapus') }}</button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Media uses contain + natural dimensions to avoid crop/upscaling blur --}}
                                @php
                                    $media = $announcement->attachments->whereIn('file_type', ['image', 'video']);
                                    $mediaCount = $media->count();
                                @endphp

                                @if($mediaCount > 0)
                                    <div class="border-y border-gray-100 bg-gray-950 dark:border-gray-700">
                                        @if($mediaCount === 1)
                                            @foreach($media as $item)
                                                @if($item->file_type === 'video')
                                                    <div class="relative mx-auto aspect-video max-h-[280px] w-full overflow-hidden bg-black sm:max-h-[360px]">
                                                        <video controls preload="metadata" class="absolute inset-0 h-full w-full object-contain">
                                                            <source src="{{ Storage::url($item->file_path) }}"
                                                                type="video/{{ pathinfo($item->file_path, PATHINFO_EXTENSION) }}">
                                                        </video>
                                                    </div>
                                                @else
                                                    <a href="{{ Storage::url($item->file_path) }}" target="_blank" class="block bg-gray-950 text-center">
                                                        <img src="{{ Storage::url($item->file_path) }}"
                                                            class="mx-auto block h-auto max-h-[360px] w-auto max-w-full object-contain [image-rendering:auto] sm:max-h-[440px]"
                                                            loading="lazy" decoding="async" fetchpriority="low">
                                                    </a>
                                                @endif
                                            @endforeach
                                        @else
                                            <div x-data="{
                                                    activeSlide: 1,
                                                    total: {{ $mediaCount }},
                                                    scrollNext() { this.$refs.slider.scrollBy({ left: this.$refs.slider.offsetWidth, behavior: 'smooth' }); },
                                                    scrollPrev() { this.$refs.slider.scrollBy({ left: -this.$refs.slider.offsetWidth, behavior: 'smooth' }); },
                                                    updateSlide() {
                                                        let width = this.$refs.slider.offsetWidth;
                                                        let scrollLeft = this.$refs.slider.scrollLeft;
                                                        this.activeSlide = Math.round(scrollLeft / width) + 1;
                                                    }
                                                }" 
                                                class="relative group bg-gray-950">
                                                
                                                <div x-ref="slider" @scroll.passive="updateSlide()" class="flex overflow-x-auto snap-x snap-mandatory [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                                                    @foreach($media as $item)
                                                        <div class="relative flex-none w-full snap-center flex items-center justify-center h-[320px] sm:h-[400px] bg-black">
                                                            @if($item->file_type === 'video')
                                                                <video controls preload="metadata" class="absolute inset-0 h-full w-full object-contain">
                                                                    <source src="{{ Storage::url($item->file_path) }}" type="video/{{ pathinfo($item->file_path, PATHINFO_EXTENSION) }}">
                                                                </video>
                                                            @else
                                                                <a href="{{ Storage::url($item->file_path) }}" target="_blank" class="flex h-full w-full items-center justify-center">
                                                                    <img src="{{ Storage::url($item->file_path) }}" class="block h-full w-full object-contain transition hover:opacity-95 [image-rendering:auto]" loading="lazy" decoding="async">
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>

                                                {{-- Controls --}}
                                                <button @click.prevent="scrollPrev()" x-show="activeSlide > 1" x-cloak class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/70 text-white rounded-full p-1.5 hidden group-hover:flex items-center justify-center backdrop-blur-sm transition">
                                                    <x-icon name="chevron-left" class="w-5 h-5" />
                                                </button>
                                                <button @click.prevent="scrollNext()" x-show="activeSlide < total" x-cloak class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/70 text-white rounded-full p-1.5 hidden group-hover:flex items-center justify-center backdrop-blur-sm transition">
                                                    <x-icon name="chevron-right" class="w-5 h-5" />
                                                </button>

                                                {{-- Indicator --}}
                                                <div class="absolute top-3 right-3 bg-black/60 backdrop-blur-md text-white text-[10px] font-bold px-2.5 py-1 rounded-full pointer-events-none tracking-widest shadow-sm border border-white/10">
                                                    <span x-text="activeSlide"></span>/{{ $mediaCount }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Compact content and documents --}}
                                <div class="p-4" x-data="{ expanded: false }">
                                    <div class="flex items-start justify-between gap-3">
                                        <h3 class="min-w-0 line-clamp-1 text-sm font-bold text-gray-900 dark:text-white">{{ $announcement->title }}</h3>
                                        @if($mediaCount > 0)
                                            <span class="shrink-0 rounded-full bg-gray-100 px-2 py-1 text-[10px] font-medium text-gray-500 dark:bg-gray-700 dark:text-gray-300">{{ $mediaCount }} {{ __('media') }}</span>
                                        @endif
                                    </div>

                                    <div class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300">
                                        @php
                                            $isLong = strlen($announcement->content) > 100 || substr_count($announcement->content, "\n") >= 2;
                                        @endphp
                                        <div :class="expanded ? '' : '{{ $isLong ? 'line-clamp-3' : '' }}'">
                                            {!! nl2br(e($announcement->content)) !!}
                                        </div>
                                        @if($isLong)
                                            <button @click="expanded = !expanded"
                                                class="mt-1 text-xs font-semibold text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                                <span x-show="!expanded">... {{ __('selengkapnya') }}</span>
                                                <span x-show="expanded">{{ __('sembunyikan') }}</span>
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Documents --}}
                                    @php
                                        $docs = $announcement->attachments->where('file_type', 'document');
                                    @endphp
                                    @if($docs->isNotEmpty())
                                        <div class="mt-3 border-t border-gray-100 pt-3 dark:border-gray-700">
                                            <div class="mb-2 flex items-center justify-between">
                                                <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">{{ __('Lampiran') }}</span>
                                                <span class="text-[10px] text-gray-400">{{ $docs->count() }} {{ __('file') }}</span>
                                            </div>
                                            <div class="max-h-36 space-y-1.5 overflow-y-auto pr-1">
                                                @foreach($docs as $doc)
                                                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                                        class="flex items-center gap-2 rounded-lg border border-gray-100 bg-gray-50/80 p-2 text-xs font-medium text-gray-600 transition hover:border-primary-100 hover:bg-primary-50 hover:text-primary-700 dark:border-gray-700 dark:bg-gray-700/40 dark:text-gray-300 dark:hover:bg-gray-600">
                                                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-white shadow-sm dark:bg-gray-800">
                                                            <x-icon name="document-text" class="h-3.5 w-3.5 text-primary-500" />
                                                        </span>
                                                        <span class="min-w-0 flex-1 truncate">{{ $doc->original_name }}</span>
                                                        <x-icon name="download" class="h-3.5 w-3.5 shrink-0 text-gray-400" />
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="rounded-2xl border border-dashed border-gray-200 py-12 text-center dark:border-gray-700 dark:bg-gray-800">
                                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-primary-50">
                                    <x-icon name="sparkles" class="h-7 w-7 text-primary-400" />
                                </div>
                                <h3 class="text-base font-medium text-gray-900 dark:text-white">{{ __('Belum Ada Kabar') }}</h3>
                                <p class="mt-1 text-sm text-gray-500">{{ __('Semua informasi penting akan muncul di sini.') }}</p>
                            </div>
                        @endforelse

                        {{-- Delete Confirmation Modal (Using Standard x-modal) --}}
                        <x-modal name="confirm-announcement-deletion" maxWidth="md" focusable>
                            <form :action="deleteUrl" method="POST" class="p-6">
                                @csrf
                                @method('delete')

                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('Hapus Pengumuman?') }}
                                </h2>

                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('Apakah Anda yakin ingin menghapus pengumuman ini? Tindakan ini tidak dapat dibatalkan.') }}
                                </p>

                                <div class="mt-6 flex justify-end">
                                    <x-secondary-button x-on:click="$dispatch('close')">
                                        {{ __('Batal') }}
                                    </x-secondary-button>

                                    <x-danger-button class="ms-3">
                                        {{ __('Hapus Pengumuman') }}
                                    </x-danger-button>
                                </div>
                            </form>
                        </x-modal>
                    </div>
                </div>
            </div>

            {{-- TAB 2: TASKS (ORIGINAL DASHBOARD) --}}
            <div x-show="tab === 'tasks'" style="display: none;" class="animate-fade-in space-y-6 xl:space-y-8">
                <!-- Statistics Cards (Enhanced with gradients) -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 xl:gap-5 stagger-enter">
                    <div class="card-elevated group relative overflow-hidden rounded-3xl p-5 hover-lift sm:p-6">
                        <div
                            class="absolute top-0 right-0 w-24 h-24 bg-primary-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="relative">
                            <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center mb-3">
                                <x-icon name="document-text" class="w-6 h-6 text-primary-600" />
                            </div>
                            <p class="text-sm text-gray-500 font-medium">{{ __('Total Dokumen') }}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_documents'] ?? 0 }}</p>
                        </div>
                        <a href="{{ route('documents.index') }}" class="absolute inset-0"></a>
                    </div>

                    <div class="card-elevated group relative overflow-hidden rounded-3xl p-5 hover-lift sm:p-6">
                        <div
                            class="absolute top-0 right-0 w-24 h-24 bg-amber-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="relative">
                            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center mb-3">
                                <x-icon name="{{ ($stats['is_approver'] ?? false) ? 'clock' : 'arrow-path' }}" class="w-6 h-6 text-amber-600" />
                            </div>
                            @if($stats['is_approver'] ?? false)
                                <p class="text-sm text-gray-500 font-medium">{{ __('Menunggu Review') }}</p>
                                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['pending_review'] ?? 0 }}</p>
                            @else
                                <p class="text-sm text-gray-500 font-medium">{{ __('Sedang Diproses') }}</p>
                                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['in_progress'] ?? 0 }}</p>
                            @endif
                        </div>
                        <a href="{{ ($stats['is_approver'] ?? false) ? route('approvals.index') : route('documents.index', ['status' => 'pending']) }}" class="absolute inset-0"></a>
                    </div>

                    <div class="card-elevated group relative overflow-hidden rounded-3xl p-5 hover-lift sm:p-6">
                        <div
                            class="absolute top-0 right-0 w-24 h-24 bg-green-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="relative">
                            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center mb-3">
                                <x-icon name="check-circle" class="w-6 h-6 text-green-600" />
                            </div>
                            <p class="text-sm text-gray-500 font-medium">{{ __('Disetujui Bulan Ini') }}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['approved_this_month'] ?? 0 }}
                            </p>
                        </div>
                    </div>

                    <div class="card-elevated group relative overflow-hidden rounded-3xl p-5 hover-lift sm:p-6">
                        <div
                            class="absolute top-0 right-0 w-24 h-24 bg-red-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="relative">
                            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center mb-3">
                                <x-icon name="x-circle" class="w-6 h-6 text-red-600" />
                            </div>
                            <p class="text-sm text-gray-500 font-medium">{{ __('Ditolak Bulan Ini') }}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['rejected_this_month'] ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Recent Documents (Enhanced) -->
                    <div class="lg:col-span-2">
                        <div class="card-elevated rounded-3xl p-5 sm:p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                                    {{ __('Dokumen Terbaru') }}
                                </h2>
                                <a href="{{ route('documents.index') }}"
                                    class="text-sm font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400 flex items-center gap-1 group">
                                    {{ __('Lihat Semua') }}
                                    <span class="group-hover:translate-x-1 transition-transform duration-200">→</span>
                                </a>
                            </div>

                            <div class="space-y-3 stagger-enter">
                                @forelse($recentDocuments ?? [] as $document)
                                    <div
                                        class="group flex items-center justify-between gap-4 rounded-2xl border border-gray-100 bg-gray-50/70 p-4 transition-all duration-300 hover:border-primary-100 hover:bg-primary-50/50">
                                        <div class="flex items-center gap-4 min-w-0 flex-1">
                                            <div
                                                class="flex-shrink-0 w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center group-hover:bg-primary-200 transition-colors">
                                                <x-icon name="document-text" class="w-5 h-5 text-primary-600" />
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <a href="{{ route('documents.show', $document) }}"
                                                    class="text-sm font-medium text-gray-900 hover:text-primary-600 truncate block">
                                                    {{ $document->title }}
                                                </a>
                                                <p class="text-xs text-gray-500 mt-0.5">
                                                    {{ $document->document_number }} •
                                                    {{ $document->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                        <x-ui.badge :status="$document->status_color">
                                            {{ $document->status_label }}
                                        </x-ui.badge>
                                    </div>
                                @empty
                                    <div class="text-center py-8">
                                        <div
                                            class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-3">
                                            <x-icon name="document-text" class="w-8 h-8 text-gray-300" />
                                        </div>
                                        <p class="text-sm text-gray-500">{{ __('Belum ada dokumen') }}</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Pending Approvals (Enhanced) -->
                    <div class="space-y-6">
                        @if(auth()->user()->hasPermissionTo('approve-document'))
                            <div class="card-elevated rounded-3xl p-5 sm:p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                                        {{ __('Menunggu Approval') }}
                                    </h2>
                                    <span
                                        class="bg-primary-100 text-primary-700 text-xs font-bold px-2.5 py-1 rounded-full">
                                        {{ $pendingApprovals->count() ?? 0 }}
                                    </span>
                                </div>

                                <div class="space-y-2 stagger-enter">
                                    @forelse($pendingApprovals ?? [] as $document)
                                        <a href="{{ route('approvals.show', $document) }}"
                                            class="group block rounded-2xl border border-transparent p-3.5 transition-all duration-300 hover:border-primary-100 hover:bg-primary-50">
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="min-w-0 flex-1">
                                                    <p
                                                        class="text-sm font-medium text-gray-900 truncate group-hover:text-primary-700 transition-colors">
                                                        {{ $document->title }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-0.5">
                                                        {{ __('dari') }} {{ $document->user->name }}
                                                    </p>
                                                </div>
                                                <x-ui.badge :status="$document->priority_color" size="sm">
                                                    {{ $document->priority_label }}
                                                </x-ui.badge>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="text-center py-6">
                                            <div
                                                class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-2">
                                                <x-icon name="check-circle" class="w-6 h-6 text-green-400" />
                                            </div>
                                            <p class="text-sm text-gray-500">{{ __('Tidak ada dokumen yang menunggu 👍') }}</p>
                                        </div>
                                    @endforelse
                                </div>

                                @if(($pendingApprovals ?? collect())->count() > 0)
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <a href="{{ route('approvals.index') }}"
                                            class="flex items-center justify-center gap-2 w-full py-2.5 text-sm font-medium text-primary-600 hover:text-primary-800 hover:bg-primary-50 rounded-xl transition-all duration-300">
                                            {{ __('Lihat Semua Approval') }}
                                            <span>→</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

</x-app-layout>