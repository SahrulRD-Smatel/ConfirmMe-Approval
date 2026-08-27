<x-app-layout>
    <x-slot name="title">{{ __('Bulk Approval') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Bulk Approval') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Setujui atau tolak beberapa dokumen sekaligus') }}
                </p>
            </div>
            <a href="{{ route('approvals.index') }}">
                <x-ui.button variant="secondary" icon="arrow-left">
                    {{ __('Kembali') }}
                </x-ui.button>
            </a>
        </div>
    </x-slot>

    @if($documents->count() > 0)
        <form action="{{ route('approvals.bulk.process') }}" method="POST" id="bulkForm" x-data="{ 
                                  selectedIds: [],
                                  action: 'approve',
                                  comment: '',
                                  selectAll: false,
                                  showConfirmModal: false,
                                  toggleAll() {
                                      if (this.selectAll) {
                                          this.selectedIds = {{ $documents->pluck('id') }};
                                      } else {
                                          this.selectedIds = [];
                                      }
                                  },
                                  submitForm() {
                                      if (this.selectedIds.length === 0) {
                                          showAlert('{{ __('Pilih minimal satu dokumen') }}');
                                          return;
                                      }
                                      this.showConfirmModal = true;
                                  }
                              }">
            @csrf

            <!-- Action Bar -->
            <x-ui.card class="mb-7">
                <div class="flex flex-col gap-5">
                    {{-- Top row: Select all & count --}}
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()"
                                class="w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500/20">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Pilih Semua') }}</span>
                        </label>
                        <span
                            class="px-3 py-1 text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-full"
                            x-text="selectedIds.length + ' {{ __('dokumen dipilih') }}'"></span>
                    </div>

                    {{-- Bottom row: Action controls --}}
                    <div
                        class="flex flex-col items-stretch gap-3 border-t border-gray-200/80 pt-4 dark:border-gray-700/80 sm:flex-row sm:items-center">
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ __('Aksi:') }}</label>
                            <select name="action" x-model="action"
                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500/20 text-sm min-w-[120px]">
                                <option value="approve">✓ {{ __('Setujui') }}</option>
                                <option value="reject">✗ {{ __('Tolak') }}</option>
                            </select>
                        </div>

                        <input type="text" name="comment" x-model="comment" placeholder="{{ __('Komentar (opsional)') }}"
                            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500/20 text-sm min-w-0">

                        <button type="button" @click="submitForm()" x-bind:disabled="selectedIds.length === 0"
                            :class="action === 'approve' ? 'bg-success-600 hover:bg-success-700' : 'bg-danger-600 hover:bg-danger-700'"
                            class="px-4 py-2 text-white font-medium rounded-xl whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-text="action === 'approve' ? '✓ ' + '{{ __('Setujui Terpilih') }}' : '✗ ' + '{{ __('Tolak Terpilih') }}'"></span>
                        </button>
                    </div>
                </div>
            </x-ui.card>

            <!-- Documents by Type -->
            @foreach($groupedDocuments as $type => $docs)
                <x-ui.card class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <span
                            class="px-2 py-1 text-xs font-medium rounded-full 
                                                            {{ $type === 'only' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' : '' }}
                                                            {{ $type === 'po' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                                            {{ $type === 'invoice' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}">
                            {{ config('workflow.type_labels.' . $type, ucfirst($type)) }}
                        </span>
                        <span class="text-sm font-normal text-gray-500">({{ $docs->count() }} {{ __('dokumen') }})</span>
                    </h3>

                    <div class="divide-y divide-gray-200/80 dark:divide-gray-700/80">
                        @foreach($docs as $document)
                            <div class="flex items-start gap-4 py-4">
                                <input type="checkbox" value="{{ $document->id }}"
                                    :checked="selectedIds.includes({{ $document->id }})"
                                    @change="$event.target.checked ? selectedIds.push({{ $document->id }}) : selectedIds = selectedIds.filter(id => id !== {{ $document->id }})"
                                    class="mt-1 w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500/20">

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('approvals.show', $document) }}"
                                            class="font-medium text-gray-900 dark:text-white hover:text-primary-600">
                                            {{ $document->title }}
                                        </a>
                                        <span
                                            class="px-2 py-0.5 text-xs rounded-full
                                                                                            {{ $document->priority === 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                                                                                            {{ $document->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                                                                                            {{ $document->priority === 'normal' ? 'bg-blue-100 text-blue-800' : '' }}
                                                                                            {{ $document->priority === 'low' ? 'bg-gray-100 text-gray-800' : '' }}">
                                            {{ $document->priority_label }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $document->document_number }} &bull;
                                        {{ __('Oleh') }} {{ $document->user->name }} &bull;
                                        {{ $document->created_at->diffForHumans() }}
                                    </p>
                                    @if($document->estimated_budget)
                                        <p class="mt-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Rp {{ number_format($document->estimated_budget, 0, ',', '.') }}
                                        </p>
                                    @endif
                                </div>

                                <a href="{{ route('approvals.show', $document) }}"
                                    class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                    {{ __('Review') }} &rarr;
                                </a>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            @endforeach

            <!-- Hidden inputs for selected document IDs -->
            <template x-for="id in selectedIds" :key="id">
                <input type="hidden" name="document_ids[]" :value="id">
            </template>

            <!-- Confirmation Modal -->
            <div x-show="showConfirmModal" x-transition
                class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/70 p-4 backdrop-blur-sm"
                @click.self="showConfirmModal = false" style="display: none;">
                <div class="w-full max-w-md rounded-3xl border border-gray-200/80 bg-white p-6 shadow-soft-lg dark:border-gray-700 dark:bg-gray-900" @click.stop>
                    <!-- Approve Confirmation -->
                    <template x-if="action === 'approve'">
                        <div>
                            <div
                                class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-success-100 dark:bg-success-900/30 rounded-full">
                                <x-icon name="check-circle" class="w-10 h-10 text-success-600" />
                            </div>
                            <h3 class="text-xl font-semibold text-center text-gray-900 dark:text-white mb-2">
                                {{ __('Konfirmasi Approval') }}
                            </h3>
                            <p class="text-center text-gray-600 dark:text-gray-400 mb-2">
                                {{ __('Anda yakin ingin') }} <strong class="text-success-600">{{ __('MENYETUJUI') }}</strong>
                            </p>
                            <p class="text-center text-lg font-bold text-gray-900 dark:text-white mb-4">
                                <span x-text="selectedIds.length"></span> {{ __('dokumen?') }}
                            </p>
                            <p class="text-center text-sm text-gray-500 mb-6">
                                {{ __('Tindakan ini tidak dapat dibatalkan.') }}
                            </p>

                            <div class="flex gap-3">
                                <button type="button" @click="showConfirmModal = false"
                                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-primary-50/60 dark:hover:bg-gray-700 transition-colors">
                                    {{ __('Batal') }}
                                </button>
                                <button type="submit"
                                    class="flex-1 px-4 py-2 bg-success-600 hover:bg-success-700 text-white rounded-xl transition-colors">
                                    {{ __('Ya, Setujui Semua') }}
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- Reject Confirmation -->
                    <template x-if="action === 'reject'">
                        <div>
                            <div
                                class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-danger-100 dark:bg-danger-900/30 rounded-full">
                                <x-icon name="x-circle" class="w-10 h-10 text-danger-600" />
                            </div>
                            <h3 class="text-xl font-semibold text-center text-gray-900 dark:text-white mb-2">
                                {{ __('Konfirmasi Penolakan') }}
                            </h3>
                            <p class="text-center text-gray-600 dark:text-gray-400 mb-2">
                                {{ __('Anda yakin ingin') }} <strong class="text-danger-600">{{ __('MENOLAK') }}</strong>
                            </p>
                            <p class="text-center text-lg font-bold text-gray-900 dark:text-white mb-4">
                                <span x-text="selectedIds.length"></span> {{ __('dokumen?') }}
                            </p>
                            <p class="text-center text-sm text-gray-500 mb-6">
                                {{ __('Tindakan ini tidak dapat dibatalkan.') }}
                            </p>

                            <div class="flex gap-3">
                                <button type="button" @click="showConfirmModal = false"
                                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-primary-50/60 dark:hover:bg-gray-700 transition-colors">
                                    {{ __('Batal') }}
                                </button>
                                <button type="submit"
                                    class="flex-1 px-4 py-2 bg-danger-600 hover:bg-danger-700 text-white rounded-xl transition-colors">
                                    {{ __('Ya, Tolak Semua') }}
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </form>
    @else
        <x-ui.card>
            <div class="text-center py-12">
                <x-icon name="check-circle" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Tidak ada dokumen menunggu approval') }}</h3>
                <p class="mt-2 text-sm text-gray-500">{{ __('Semua dokumen sudah diproses.') }}</p>
            </div>
        </x-ui.card>
    @endif
</x-app-layout>