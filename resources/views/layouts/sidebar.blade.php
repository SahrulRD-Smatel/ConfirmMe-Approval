<!-- Sidebar -->
<!-- Mobile: slide in/out via x-show + x-transition -->
<!-- Desktop: always visible, collapse via width class -->
<aside class="fixed inset-y-0 left-0 z-50 flex flex-col bg-primary-800 dark:bg-gray-800 shadow-[4px_0_24px_-2px_rgba(0,0,0,0.2)] sidebar-modern"
    x-cloak
    :class="[
           sidebarCollapsed ? 'w-20' : 'w-64',
           'transition-[width] duration-300 ease-in-out',
           sidebarOpen ? 'translate-x-0' : '-translate-x-full',
           'lg:translate-x-0'
       ]"
    style="transition: width 300ms ease-in-out, transform 300ms ease-in-out;">

    <!-- Logo Section -->
    <div class="flex h-16 shrink-0 items-center justify-between px-4 border-b border-primary-700/40">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="flex-shrink-0 w-10 h-10 bg-white rounded-lg flex items-center justify-center overflow-hidden">
                <img src="{{ asset('images/logo-infodata.jpg') }}" alt="Infodata" class="w-full h-full object-cover">
            </div>
            <span class="text-white font-display font-bold text-xl transition-opacity duration-300"
                :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0' : 'opacity-100'">
                ConfirmMe
            </span>
        </a>

        <!-- Mobile close button -->
        <button @click="sidebarOpen = false" class="lg:hidden text-primary-200 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Desktop collapse button -->
        <button @click="sidebarCollapsed = !sidebarCollapsed"
            class="hidden lg:flex text-primary-200 hover:text-white p-1 rounded">
            <svg class="w-5 h-5 transition-transform" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>
    </div>


    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        {{-- Dashboard - All Roles except Presiden Direktur (has Executive Dashboard) --}}
        @if(!auth()->user()->hasRole('presiden_direktur'))
            <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
                Dashboard
            </x-sidebar-link>
        @endif

        {{-- Dokumen Menu - Based on permissions (Not for Presiden Direktur) --}}
        @if(!auth()->user()->hasRole('presiden_direktur') && (auth()->user()->hasPermissionTo('create-document') || auth()->user()->hasPermissionTo('view-document')))
            <x-sidebar-section title="Dokumen" :collapsed="$sidebarCollapsed ?? false">
                @can('create', App\Models\Document::class)
                    <x-sidebar-link :href="route('documents.create')" :active="request()->routeIs('documents.create')"
                        icon="plus-circle">
                        Buat Pengajuan
                    </x-sidebar-link>
                @endcan
                @if(auth()->user()->hasPermissionTo('view-document'))
                    <x-sidebar-link :href="route('documents.index')" :active="request()->routeIs('documents.index')"
                        icon="document-text">
                        Daftar Pengajuan
                    </x-sidebar-link>
                @endif
                @if(feature_active('module_loan') && auth()->user()->hasPermissionTo('create-document') && !auth()->user()->hasRole('admin'))
                    <x-sidebar-link :href="route('my-loans.index')" :active="request()->routeIs('my-loans.*')"
                        icon="hand-raised">
                        Pinjaman Saya
                    </x-sidebar-link>
                @endif
                @if(feature_active('module_hris') && !auth()->user()->hasRole('admin'))
                    <x-sidebar-link :href="route('leave-requests.index')" :active="request()->routeIs('leave-requests.*')"
                        icon="calendar">
                        Pengajuan Cuti
                    </x-sidebar-link>
                @endif
            </x-sidebar-section>
        @endif

        {{-- Absensi - Active employees --}}
        @if(feature_active('module_hris') && auth()->user()->roles->isNotEmpty())
            <x-sidebar-section title="Absensi" :collapsed="$sidebarCollapsed ?? false">
                <x-sidebar-link :href="route('attendance.index')" :active="request()->routeIs('attendance.index')"
                    icon="clock">
                    Check-in / Out
                </x-sidebar-link>
                <x-sidebar-link :href="route('attendance.history')" :active="request()->routeIs('attendance.history')"
                    icon="calendar-days">
                    Riwayat Absensi
                </x-sidebar-link>
            </x-sidebar-section>
        @endif

        {{-- Approval Menu - Dynamic via Permissions --}}
        @if(auth()->user()->hasPermissionTo('approve-document'))
            {{-- Presiden Direktur uses Executive section instead --}}
            @if(!auth()->user()->hasRole('presiden_direktur'))
                <x-sidebar-section title="Approval" :collapsed="$sidebarCollapsed ?? false">
                    <x-sidebar-link :href="route('approvals.index')" :active="request()->routeIs('approvals.index')"
                        icon="inbox-arrow-down">
                        Approval Queue
                        @if(($sidebarBadges['pendingApprovalCount'] ?? 0) > 0)
                            <span
                                class="ml-auto bg-danger-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $sidebarBadges['pendingApprovalCount'] }}</span>
                        @endif
                    </x-sidebar-link>
                    @if(!auth()->user()->hasRole('presiden_direktur'))
                        <x-sidebar-link :href="route('approvals.bulk')" :active="request()->routeIs('approvals.bulk')"
                            icon="squares-2x2">
                            Bulk Approval
                        </x-sidebar-link>
                    @endif
                    <x-sidebar-link :href="route('approvals.history')" :active="request()->routeIs('approvals.history')"
                        icon="clock">
                        Riwayat Approval
                    </x-sidebar-link>
                </x-sidebar-section>
            @endif
        @endif

        {{-- Leave Approval Menu --}}
        @if(feature_active('module_hris') && auth()->user()->hasPermissionTo('approve-document') && !auth()->user()->hasRole('presiden_direktur'))
            @php $pendingLeaveCount = $sidebarBadges['pendingLeaveCount'] ?? 0; @endphp

            <x-sidebar-section title="Cuti" :collapsed="$sidebarCollapsed ?? false">
                <x-sidebar-link :href="route('leave-approvals.index')" :active="request()->routeIs('leave-approvals.*')"
                    icon="user-group">
                    Approval Cuti
                    @if($pendingLeaveCount > 0)
                        <span
                            class="ml-auto bg-orange-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingLeaveCount }}</span>
                    @endif
                </x-sidebar-link>
            </x-sidebar-section>
        @endif

        {{-- Executive Menu - Presiden Direktur only --}}
        @if(auth()->user()->hasRole('presiden_direktur'))
            {{-- Executive Dashboard at top level --}}
            <x-sidebar-link :href="route('executive.dashboard')" :active="request()->routeIs('executive.dashboard')"
                icon="building-office-2">
                Dashboard
            </x-sidebar-link>

            <x-sidebar-section title="Executive" :collapsed="$sidebarCollapsed ?? false">
                <x-sidebar-link :href="route('executive.approvals')" :active="request()->routeIs('executive.approvals*')"
                    icon="inbox-stack">
                    Approval Queue
                    @if(($sidebarBadges['presdirPendingCount'] ?? 0) > 0)
                        <span
                            class="ml-auto bg-purple-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $sidebarBadges['presdirPendingCount'] }}</span>
                    @endif
                </x-sidebar-link>
                @if(feature_active('module_hris'))
                    @php $presdirLeaveCount = $sidebarBadges['presdirLeaveCount'] ?? 0; @endphp
                    <x-sidebar-link :href="route('leave-approvals.index')" :active="request()->routeIs('leave-approvals.*')"
                        icon="calendar-days">
                        Approval Cuti
                        @if($presdirLeaveCount > 0)
                            <span
                                class="ml-auto bg-orange-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $presdirLeaveCount }}</span>
                        @endif
                    </x-sidebar-link>
                @endif
                <x-sidebar-link :href="route('executive.insights')" :active="request()->routeIs('executive.insights')"
                    icon="presentation-chart-bar">
                    Company Insights
                </x-sidebar-link>
                <x-sidebar-link :href="route('executive.audit')" :active="request()->routeIs('executive.audit')"
                    icon="clock">
                    Audit & History
                </x-sidebar-link>
            </x-sidebar-section>
        @endif

        <!-- Finance Menu - Dynamic via Permissions -->
        @if(feature_active('module_finance') && auth()->user()->hasAnyPermission(['manage-budget', 'create-po', 'create-invoice', 'print-po', 'print-invoice', 'manage-purchasing']))
            <x-sidebar-section title="Keuangan" :collapsed="$sidebarCollapsed ?? false">

                <x-sidebar-link :href="route('finance.budgets.index')" :active="request()->routeIs('finance.budgets.*')"
                    icon="banknotes">
                    Manajemen Anggaran
                </x-sidebar-link>
                <x-sidebar-link :href="route('finance.po.index')" :active="request()->routeIs('finance.po.*')"
                    icon="document-duplicate">
                    Purchase Orders
                    @if(($sidebarBadges['pendingPOCount'] ?? 0) > 0)
                        <span
                            class="ml-auto bg-primary-500 text-white text-xs font-bold px-2 py-0.5 rounded-full animate-pulse">{{ $sidebarBadges['pendingPOCount'] }}</span>
                    @endif
                </x-sidebar-link>
                <x-sidebar-link :href="route('finance.invoices.index')" :active="request()->routeIs('finance.invoices.*')"
                    icon="receipt-percent">
                    Invoices
                    @if(($sidebarBadges['pendingInvoiceCount'] ?? 0) > 0)
                        <span
                            class="ml-auto bg-orange-500 text-white text-xs font-bold px-2 py-0.5 rounded-full animate-pulse">{{ $sidebarBadges['pendingInvoiceCount'] }}</span>
                    @endif
                </x-sidebar-link>
                @if(feature_active('module_loan'))
                    <x-sidebar-link :href="route('finance.loans.index')" :active="request()->routeIs('finance.loans.*')"
                        icon="currency-dollar">
                        Pinjaman Karyawan
                        @if(($sidebarBadges['pendingLoanCount'] ?? 0) > 0)
                            <span
                                class="ml-auto bg-yellow-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $sidebarBadges['pendingLoanCount'] }}</span>
                        @endif
                    </x-sidebar-link>
                @endif
            </x-sidebar-section>
        @endif

        <!-- Supervisory View - Dynamic via Permissions -->
        @if(auth()->user()->hasPermissionTo('approve-document'))
            <x-sidebar-section title="Tim Saya" :collapsed="$sidebarCollapsed ?? false">
                <x-sidebar-link :href="route('team.documents')" :active="request()->routeIs('team.*')" icon="user-group">
                    Dokumen Tim
                </x-sidebar-link>
            </x-sidebar-section>
        @endif

        {{-- Reports - Dynamic via Permissions --}}
        @if(auth()->user()->hasPermissionTo('view-reports'))
            <x-sidebar-section title="Laporan" :collapsed="$sidebarCollapsed ?? false">
                <x-sidebar-link :href="route('reports.index')" :active="request()->routeIs('reports.*')" icon="chart-bar">
                    Laporan
                </x-sidebar-link>
            </x-sidebar-section>
        @endif

        {{-- Attendance & Leave Admin - Dynamic via Permissions --}}
        @if(feature_active('module_hris') && auth()->user()->hasAnyPermission(['manage-attendance', 'view-attendance']))
            <x-sidebar-section title="Kelola Karyawan" :collapsed="$sidebarCollapsed ?? false">
                <x-sidebar-link :href="route('attendance-admin.report')" :active="request()->routeIs('attendance-admin.report')"
                    icon="chart-bar-square">
                    Laporan Absensi
                </x-sidebar-link>
                <x-sidebar-link :href="route('leave-reports.index')" :active="request()->routeIs('leave-reports.*')"
                    icon="document-duplicate">
                    Laporan Cuti
                </x-sidebar-link>
                @if(auth()->user()->hasAnyRole(['admin', 'hrd', 'direktur_hr']))
                    <x-sidebar-link :href="route('attendance-admin.locations')" :active="request()->routeIs('attendance-admin.locations')"
                        icon="map-pin">
                        Lokasi Kantor
                    </x-sidebar-link>
                @endif
            </x-sidebar-section>
        @endif

        {{-- Pengumuman - Dynamic via Permissions --}}
        @if(feature_active('module_hris') && (auth()->user()->hasPermissionTo('manage-attendance') || auth()->user()->department === 'Human Resources'))
            <x-sidebar-section title="HRIS" :collapsed="$sidebarCollapsed ?? false">
                <x-sidebar-link :href="route('announcements.index')" :active="request()->routeIs('announcements.*')"
                    icon="megaphone">
                    Pengumuman
                </x-sidebar-link>
            </x-sidebar-section>
        @endif

        <!-- Admin Menu - Dynamic via Permissions -->
        @canany(['manage-users', 'manage-roles', 'manage-features', 'manage-workflow', 'manage-departments', 'manage-templates', 'view-audit-log'])
            <x-sidebar-section title="Administrasi" :collapsed="$sidebarCollapsed ?? false">
                @can('manage-users')
                <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')"
                    icon="users">
                    Manajemen User
                </x-sidebar-link>
                @endcan
                @can('manage-roles')
                <x-sidebar-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')"
                    icon="shield-check">
                    Role & Permission
                </x-sidebar-link>
                @endcan
                @can('manage-features')
                <x-sidebar-link :href="route('admin.features.index')" :active="request()->routeIs('admin.features.*')"
                    icon="adjustments-horizontal">
                    Fitur & Modul
                </x-sidebar-link>
                @endcan
                @can('manage-workflow')
                <x-sidebar-link :href="route('admin.workflow.index')" :active="request()->routeIs('admin.workflow.*')"
                    icon="cog-6-tooth">
                    Konfigurasi Workflow
                </x-sidebar-link>
                @endcan
                @can('manage-departments')
                <x-sidebar-link :href="route('admin.departments.index')" :active="request()->routeIs('admin.departments.*')"
                    icon="building-office">
                    Departemen
                </x-sidebar-link>
                @endcan
                @can('manage-templates')
                <x-sidebar-link :href="route('admin.templates.index')" :active="request()->routeIs('admin.templates.*')"
                    icon="document-text">
                    Template Dokumen
                </x-sidebar-link>
                @endcan
                @can('view-audit-log')
                <x-sidebar-link :href="route('admin.audit-logs.index')" :active="request()->routeIs('admin.audit-logs.*')"
                    icon="clipboard-document-list">
                    Audit Log
                </x-sidebar-link>
                @endcan
            </x-sidebar-section>
        @endcanany

        {{-- Archive - All users with view-document permission (not Presiden Direktur) --}}
        @if(!auth()->user()->hasRole('presiden_direktur') && auth()->user()->hasPermissionTo('view-document'))
            <x-sidebar-section title="Arsip" :collapsed="$sidebarCollapsed ?? false">
                <x-sidebar-link :href="route('archive.index')" :active="request()->routeIs('archive.*')" icon="archive-box">
                    Riwayat Dokumen
                </x-sidebar-link>
            </x-sidebar-section>
        @endif
    </nav>

    <!-- Bottom Section -->
    <div class="shrink-0 border-t border-primary-700/40 p-4">
        <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')" icon="user-circle">
            Profil
        </x-sidebar-link>
        <form method="POST" action="{{ route('logout') }}" class="mt-1">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-3 py-2 text-primary-200 hover:text-white hover:bg-primary-700 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Keluar</span>
            </button>
        </form>
    </div>
</aside>