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
                    this.greeting = 'Selamat Pagi';
                    this.greetingIcon = '☀️';
                    this.subtitle = 'Semoga harimu menyenangkan! 🌟';
                } else if (hours >= 12 && hours < 15) {
                    this.greeting = 'Selamat Siang';
                    this.greetingIcon = '🌤️';
                    this.subtitle = 'Tetap semangat ya! 💪';
                } else if (hours >= 15 && hours < 18) {
                    this.greeting = 'Selamat Sore';
                    this.greetingIcon = '🌅';
                    this.subtitle = 'Sedikit lagi, kamu pasti bisa! ✨';
                } else {
                    this.greeting = 'Selamat Malam';
                    this.greetingIcon = '🌙';
                    this.subtitle = 'Istirahat yang cukup ya! 💤';
                }
            }
        }">
            {{-- Clean White Header (Good Morning Style) --}}
            <div class="card-elevated mb-6 -mt-4 mx-[-1rem] sm:mx-0 p-6 sm:p-8 relative overflow-hidden">
                {{-- Subtle decorative background --}}
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-primary-50/60 to-transparent rounded-full -mr-20 -mt-20 pointer-events-none">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-40 h-40 bg-gradient-to-tr from-blue-50/40 to-transparent rounded-full -ml-16 -mb-16 pointer-events-none">
                </div>

                <div class="relative z-10 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6">
                    {{-- Left: Greeting + Clock --}}
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-2xl" x-text="greetingIcon">☀️</span>
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 font-display">
                                <span x-text="greeting">Selamat Pagi</span>, {{ auth()->user()->name }}!
                            </h1>
                        </div>
                        <p class="text-sm text-gray-400 ml-9" x-text="subtitle">Semoga harimu menyenangkan! 🌟</p>

                        {{-- Big Clock --}}
                        <div class="mt-4 ml-9">
                            <span class="text-4xl sm:text-5xl font-bold text-gray-900 font-display tracking-tight"
                                x-text="currentTime">07.53 am</span>
                        </div>
                    </div>

                    {{-- Right: Tabs + Button --}}
                    <div class="flex flex-col items-end gap-3 sm:mt-1">
                        {{-- Tab Switcher --}}
                        <div class="flex p-1 bg-gray-100 rounded-xl">
                            <button @click="tab = 'workspace'"
                                :class="{ 'bg-white shadow-md text-primary-700 font-semibold': tab === 'workspace', 'text-gray-500 hover:text-gray-700': tab !== 'workspace' }"
                                class="px-5 py-2.5 text-sm font-medium rounded-lg transition-all duration-300">
                                Workspace
                            </button>
                            <button @click="tab = 'tasks'"
                                :class="{ 'bg-white shadow-md text-primary-700 font-semibold': tab === 'tasks', 'text-gray-500 hover:text-gray-700': tab !== 'tasks' }"
                                class="px-5 py-2.5 text-sm font-medium rounded-lg transition-all duration-300">
                                Tasks & Approval
                            </button>
                        </div>

                        @if(auth()->user()->hasRole('staff'))
                            <a href="{{ route('documents.create') }}"
                                class="hidden sm:inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-primary-500/25">
                                <x-icon name="plus-circle" class="w-4 h-4" />
                                Buat Pengajuan
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- TAB 1: WORKSPACE (HRIS) --}}
            <div x-show="tab === 'workspace'" x-data="{ deleteUrl: '' }"
                class="grid grid-cols-1 lg:grid-cols-4 gap-6 animate-fade-in">
                {{-- Left Column: Profile & Quick Links --}}
                <div class="space-y-6 stagger-enter">
                    {{-- Profile Card (Enhanced) --}}
                    <div class="relative card-elevated overflow-hidden hover-lift shimmer-hover">
                        {{-- Decorative top gradient --}}
                        <div class="h-20 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900 relative">
                            <div class="absolute inset-0 dot-pattern opacity-20"></div>
                        </div>

                        <div class="px-6 pb-6 text-center -mt-12 relative z-10">
                            {{-- Avatar with gradient ring --}}
                            <div class="avatar-ring inline-block mx-auto mb-4 shadow-lg">
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                    class="w-20 h-20 object-cover">
                            </div>

                            <h3 class="text-lg font-bold text-gray-900">{{ auth()->user()->name }}</h3>
                            <p class="text-sm text-gray-500 mb-1">{{ auth()->user()->position ?? 'Karyawan' }}</p>

                            {{-- Stats mini cards --}}
                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <div class="stat-mini bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl">
                                    <span
                                        class="block text-2xl font-bold text-primary-700">{{ auth()->user()->annual_leave_balance }}</span>
                                    <span class="text-[11px] font-medium text-primary-600/70">Sisa Cuti</span>
                                </div>
                                <div class="stat-mini bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl">
                                    <span
                                        class="block text-2xl font-bold text-amber-700">{{ auth()->user()->sick_leave_used }}</span>
                                    <span class="text-[11px] font-medium text-amber-600/70">Sakit (Hari)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Links (Enhanced) --}}
                    <div class="card-elevated p-5">
                        <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                            Quick Links
                        </h3>
                        <div class="space-y-1">
                            <a href="{{ route('profile.edit') }}" class="quick-link-card">
                                <div class="icon-wrap bg-blue-100">
                                    <x-icon name="user" class="w-5 h-5 text-blue-600" />
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Edit Profil</span>
                                    <p class="text-[11px] text-gray-400">Perbarui data diri</p>
                                </div>
                            </a>
                            @if(!auth()->user()->hasRole('admin'))
                            <a href="{{ route('leave-requests.create') }}" class="quick-link-card">
                                <div class="icon-wrap bg-green-100">
                                    <x-icon name="calendar" class="w-5 h-5 text-green-600" />
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Request Cuti</span>
                                    <p class="text-[11px] text-gray-400">Ajukan permohonan</p>
                                </div>
                            </a>
                            @endif
                        </div>
                    </div>

                    {{-- Who's Off Widget (Timeline Style) --}}
                    <div class="card-elevated p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                                Who's Off Today
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
                                    <p class="text-sm text-gray-400">Semua hadir 🎉</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Center Column: Announcements (Instagram Style) --}}
                <div class="lg:col-span-3 space-y-8">
                    {{-- HR Banner (Only for HR) --}}
                    @if(auth()->user()->hasAnyRole(['hrd', 'direktur_hr', 'admin']) || auth()->user()->department === 'Human Resources')
                        <div
                            class="bg-gradient-to-r from-pink-500 to-rose-500 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden hover-lift">
                            <div class="relative z-10 flex justify-between items-center">
                                <div>
                                    <h2 class="text-xl font-bold mb-1">Halo Tim HR, {{ auth()->user()->name }}!</h2>
                                    <p class="text-pink-100 text-sm">Kelola pengumuman dan informasi perusahaan di sini.</p>
                                </div>
                                <x-ui.button href="{{ route('announcements.index') }}" variant="secondary" size="sm">
                                    Kelola Pengumuman
                                </x-ui.button>
                            </div>
                            <div class="absolute right-0 bottom-0 opacity-10 transform translate-x-10 translate-y-10">
                                <x-icon name="megaphone" class="w-40 h-40" />
                            </div>
                            {{-- Decorative circles --}}
                            <div class="absolute top-3 right-20 w-16 h-16 rounded-full bg-white/10 animate-float"></div>
                            <div class="absolute bottom-3 right-48 w-8 h-8 rounded-full bg-white/10 animate-float"
                                style="animation-delay: 1.5s;"></div>
                        </div>
                    @endif

                    {{-- Feed Container (Narrower for focus) --}}
                    <div class="max-w-xl mx-auto space-y-6 stagger-enter">
                        <div class="flex items-center gap-2 mb-2 px-2">
                            <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center">
                                <x-icon name="sparkles" class="w-4 h-4 text-primary-600" />
                            </div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Feed Terbaru</h3>
                            <div class="flex-1 h-px bg-gradient-to-r from-gray-200 to-transparent ml-2"></div>
                        </div>

                        @forelse($announcements as $announcement)
                            <div class="feed-card dark:bg-gray-800 dark:border-gray-700">
                                {{-- Card Header: Avatar & Name (Compact) --}}
                                <div class="px-4 py-3 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="relative">
                                            <img src="{{ $announcement->creator->avatar_url }}"
                                                class="w-10 h-10 rounded-full object-cover border-2 border-gray-100 dark:border-gray-700 shadow-sm">
                                            @if($announcement->type === 'urgent' || $announcement->type === 'danger')
                                                <span class="absolute -bottom-0.5 -right-0.5 flex h-3 w-3">
                                                    <span
                                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                    <span
                                                        class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-white dark:border-gray-800"></span>
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <h4
                                                class="text-sm font-bold text-gray-900 dark:text-white leading-tight flex items-center gap-1.5">
                                                {{ $announcement->creator->name }}
                                                @if($announcement->type !== 'info')
                                                                                        <span class="text-[10px] uppercase px-1.5 py-0.5 rounded-full font-bold
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
                                            <p class="text-[11px] text-gray-500 dark:text-gray-400"
                                                title="{{ $announcement->created_at }}">
                                                {{ $announcement->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Actions --}}
                                    @if(auth()->user()->hasAnyRole(['hrd', 'direktur_hr', 'admin']) || auth()->user()->department === 'Human Resources')
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" @click.away="open = false"
                                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                                <x-icon name="dots-horizontal" class="w-5 h-5" />
                                            </button>
                                            <div x-show="open" style="display: none;"
                                                class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 border border-gray-200 dark:border-gray-700 z-10 transition-all">
                                                <a href="{{ route('announcements.edit', $announcement) }}"
                                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Edit
                                                    Pengumuman</a>
                                                <button
                                                    @click="deleteUrl = '{{ route('announcements.destroy', $announcement) }}'; open = false; $dispatch('open-modal', 'confirm-announcement-deletion')"
                                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700">Hapus</button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Media (Full Width) --}}
                                @php
                                    $media = $announcement->attachments->whereIn('file_type', ['image', 'video']);
                                    $mediaCount = $media->count();
                                @endphp

                                @if($mediaCount > 0)
                                    <div class="w-full bg-black border-y border-gray-100 dark:border-gray-700">
                                        @if($mediaCount === 1)
                                            @foreach($media as $item)
                                                @if($item->file_type === 'video')
                                                    <div class="relative w-full" style="padding-top: 100%;">
                                                        <video controls class="absolute inset-0 w-full h-full object-contain">
                                                            <source src="{{ Storage::url($item->file_path) }}"
                                                                type="video/{{ pathinfo($item->file_path, PATHINFO_EXTENSION) }}">
                                                        </video>
                                                    </div>
                                                @else
                                                    <div
                                                        class="flex items-center justify-center bg-gray-100 dark:bg-gray-900 max-h-[500px] overflow-hidden">
                                                        <img src="{{ Storage::url($item->file_path) }}"
                                                            class="w-full h-auto object-cover max-h-[500px]">
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <div
                                                class="grid h-[350px] {{ $mediaCount === 2 ? 'grid-cols-2' : 'grid-cols-2' }} gap-0.5 bg-white dark:bg-gray-800">
                                                @foreach($media->take(4) as $index => $item)
                                                    <div
                                                        class="relative h-full w-full overflow-hidden {{ $mediaCount === 3 && $index === 0 ? 'row-span-2' : '' }}">
                                                        @if($item->file_type === 'video')
                                                            <video controls class="absolute inset-0 w-full h-full object-cover">
                                                                <source src="{{ Storage::url($item->file_path) }}"
                                                                    type="video/{{ pathinfo($item->file_path, PATHINFO_EXTENSION) }}">
                                                            </video>
                                                        @else
                                                            <a href="{{ Storage::url($item->file_path) }}" target="_blank">
                                                                <img src="{{ Storage::url($item->file_path) }}"
                                                                    class="absolute inset-0 w-full h-full object-cover hover:opacity-95 transition">
                                                            </a>
                                                        @endif

                                                        @if($loop->iteration === 4 && $mediaCount > 4)
                                                            <div
                                                                class="absolute inset-0 bg-black/50 flex items-center justify-center text-white font-bold text-xl pointer-events-none">
                                                                +{{ $mediaCount - 4 }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Content & Interaction --}}
                                <div class="p-4" x-data="{ expanded: false }">
                                    <h3 class="font-bold text-gray-900 dark:text-white mb-1 text-sm block">
                                        {{ $announcement->title }}
                                    </h3>

                                    <div class="relative text-sm text-gray-800 dark:text-gray-300 leading-relaxed">
                                        <div :class="expanded ? '' : 'line-clamp-2'">
                                            {!! nl2br(e($announcement->content)) !!}
                                        </div>
                                        @if(strlen($announcement->content) > 100)
                                            <button @click="expanded = !expanded"
                                                class="text-primary-600 hover:text-primary-800 dark:text-primary-400 text-xs font-semibold mt-1">
                                                <span x-show="!expanded">... selengkapnya</span>
                                                <span x-show="expanded">sembunyikan</span>
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Documents --}}
                                    @php
                                        $docs = $announcement->attachments->where('file_type', 'document');
                                    @endphp
                                    @if($docs->isNotEmpty())
                                        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                            <div class="flex flex-col gap-1.5">
                                                @foreach($docs as $doc)
                                                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                                        class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-gray-600 transition-all duration-300">
                                                        <div class="p-1 bg-white dark:bg-gray-800 rounded shadow-sm">
                                                            <x-icon name="document-text" class="w-3.5 h-3.5 text-primary-500" />
                                                        </div>
                                                        <span class="flex-1 truncate">{{ $doc->original_name }}</span>
                                                        <x-icon name="download" class="w-3.5 h-3.5 text-gray-400" />
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div
                                class="text-center py-16 card-elevated border-dashed dark:bg-gray-800 dark:border-gray-700">
                                <div
                                    class="bg-primary-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <x-icon name="sparkles" class="w-8 h-8 text-primary-400" />
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum Ada Kabar</h3>
                                <p class="text-gray-500 text-sm mt-1">Semua informasi penting akan muncul di sini.</p>
                            </div>
                        @endforelse

                        {{-- Delete Confirmation Modal (Using Standard x-modal) --}}
                        <x-modal name="confirm-announcement-deletion" maxWidth="md" focusable>
                            <form :action="deleteUrl" method="POST" class="p-6">
                                @csrf
                                @method('delete')

                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Hapus Pengumuman?
                                </h2>

                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Apakah Anda yakin ingin menghapus pengumuman ini? Tindakan ini tidak dapat
                                    dibatalkan.
                                </p>

                                <div class="mt-6 flex justify-end">
                                    <x-secondary-button x-on:click="$dispatch('close')">
                                        Batal
                                    </x-secondary-button>

                                    <x-danger-button class="ms-3">
                                        Hapus Pengumuman
                                    </x-danger-button>
                                </div>
                            </form>
                        </x-modal>
                    </div>
                </div>
            </div>

            {{-- TAB 2: TASKS (ORIGINAL DASHBOARD) --}}
            <div x-show="tab === 'tasks'" style="display: none;" class="animate-fade-in space-y-8">
                <!-- Statistics Cards (Enhanced with gradients) -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 stagger-enter">
                    <div class="relative card-elevated p-6 hover-lift overflow-hidden group">
                        <div
                            class="absolute top-0 right-0 w-24 h-24 bg-primary-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="relative">
                            <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center mb-3">
                                <x-icon name="document-text" class="w-6 h-6 text-primary-600" />
                            </div>
                            <p class="text-sm text-gray-500 font-medium">Total Dokumen</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_documents'] ?? 0 }}</p>
                        </div>
                        <a href="{{ route('documents.index') }}" class="absolute inset-0"></a>
                    </div>

                    <div class="relative card-elevated p-6 hover-lift overflow-hidden group">
                        <div
                            class="absolute top-0 right-0 w-24 h-24 bg-amber-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="relative">
                            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center mb-3">
                                <x-icon name="{{ ($stats['is_approver'] ?? false) ? 'clock' : 'arrow-path' }}" class="w-6 h-6 text-amber-600" />
                            </div>
                            @if($stats['is_approver'] ?? false)
                                <p class="text-sm text-gray-500 font-medium">Menunggu Review</p>
                                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['pending_review'] ?? 0 }}</p>
                            @else
                                <p class="text-sm text-gray-500 font-medium">Sedang Diproses</p>
                                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['in_progress'] ?? 0 }}</p>
                            @endif
                        </div>
                        <a href="{{ ($stats['is_approver'] ?? false) ? route('approvals.index') : route('documents.index', ['status' => 'pending']) }}" class="absolute inset-0"></a>
                    </div>

                    <div class="relative card-elevated p-6 hover-lift overflow-hidden group">
                        <div
                            class="absolute top-0 right-0 w-24 h-24 bg-green-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="relative">
                            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center mb-3">
                                <x-icon name="check-circle" class="w-6 h-6 text-green-600" />
                            </div>
                            <p class="text-sm text-gray-500 font-medium">Disetujui Bulan Ini</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['approved_this_month'] ?? 0 }}
                            </p>
                        </div>
                    </div>

                    <div class="relative card-elevated p-6 hover-lift overflow-hidden group">
                        <div
                            class="absolute top-0 right-0 w-24 h-24 bg-red-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="relative">
                            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center mb-3">
                                <x-icon name="x-circle" class="w-6 h-6 text-red-600" />
                            </div>
                            <p class="text-sm text-gray-500 font-medium">Ditolak Bulan Ini</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['rejected_this_month'] ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Recent Documents (Enhanced) -->
                    <div class="lg:col-span-2">
                        <div class="card-elevated p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                                    Dokumen Terbaru
                                </h2>
                                <a href="{{ route('documents.index') }}"
                                    class="text-sm font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400 flex items-center gap-1 group">
                                    Lihat Semua
                                    <span class="group-hover:translate-x-1 transition-transform duration-200">→</span>
                                </a>
                            </div>

                            <div class="space-y-3 stagger-enter">
                                @forelse($recentDocuments ?? [] as $document)
                                    <div
                                        class="flex items-center justify-between p-4 rounded-xl bg-gray-50/50 border border-gray-100 hover:bg-primary-50/50 hover:border-primary-100 transition-all duration-300 group">
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
                                        <p class="text-sm text-gray-500">Belum ada dokumen</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Pending Approvals (Enhanced) -->
                    <div class="space-y-6">
                        @if(auth()->user()->hasAnyRole(['supervisor', 'manager', 'hrd', 'hrd_coordinator', 'purchasing', 'direktur_hr', 'direktur_finance', 'direktur_it', 'direktur_sales', 'direktur_operasional', 'finance']))
                            <div class="card-elevated p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                                        Menunggu Approval
                                    </h2>
                                    <span
                                        class="bg-primary-100 text-primary-700 text-xs font-bold px-2.5 py-1 rounded-full">
                                        {{ $pendingApprovals->count() ?? 0 }}
                                    </span>
                                </div>

                                <div class="space-y-2 stagger-enter">
                                    @forelse($pendingApprovals ?? [] as $document)
                                        <a href="{{ route('approvals.show', $document) }}"
                                            class="block p-3 rounded-xl hover:bg-primary-50 border border-transparent hover:border-primary-100 transition-all duration-300 group">
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="min-w-0 flex-1">
                                                    <p
                                                        class="text-sm font-medium text-gray-900 truncate group-hover:text-primary-700 transition-colors">
                                                        {{ $document->title }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-0.5">
                                                        dari {{ $document->user->name }}
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
                                            <p class="text-sm text-gray-500">Tidak ada dokumen yang menunggu 👍</p>
                                        </div>
                                    @endforelse
                                </div>

                                @if(($pendingApprovals ?? collect())->count() > 0)
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <a href="{{ route('approvals.index') }}"
                                            class="flex items-center justify-center gap-2 w-full py-2.5 text-sm font-medium text-primary-600 hover:text-primary-800 hover:bg-primary-50 rounded-xl transition-all duration-300">
                                            Lihat Semua Approval
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