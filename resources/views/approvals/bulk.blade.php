<x-app-layout>
    <x-slot name="title">Bulk Approval</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-display">Bulk Approval</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Setujui atau tolak beberapa dokumen sekaligus
                </p>
            </div>
            <a href="{{ route('approvals.index') }}">
                <x-ui.button variant="secondary" icon="arrow-left">
                    Kembali
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
                                          showAlert('Pilih minimal satu dokumen');
                                          return;
                                      }
                                      this.showConfirmModal = true;
                                  }
                              }">
            @csrf

            <!-- Action Bar -->
            <x-ui.card class="mb-6">
                <div class="flex flex-col gap-4">
                    {{-- Top row: Select all & count --}}
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()"
                                class="w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Semua</span>
                        </label>
                        <span
                            class="px-3 py-1 text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-full"
                            x-text="selectedIds.length + ' dokumen dipilih'"></span>
                    </div>

                    {{-- Bottom row: Action controls --}}
                    <div
                        class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">Aksi:</label>
                            <select name="action" x-model="action"
                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 text-sm min-w-[120px]">
                                <option value="approve">✓ Setujui</option>
                                <option value="reject">✗ Tolak</option>
                            </select>
                        </div>

                        <input type="text" name="comment" x-model="comment" placeholder="Komentar (opsional)"
                            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 text-sm min-w-0">

                        <button type="button" @click="submitForm()" x-bind:disabled="selectedIds.length === 0"
                            :class="action === 'approve' ? 'bg-success-600 hover:bg-success-700' : 'bg-danger-600 hover:bg-danger-700'"
                            class="px-4 py-2 text-white font-medium rounded-lg whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-text="action === 'approve' ? '✓ Setujui Terpilih' : '✗ Tolak Terpilih'"></span>
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
                        <span class="text-sm font-normal text-gray-500">({{ $docs->count() }} dokumen)</span>
                    </h3>

                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($docs as $document)
                            <div class="py-4 flex items-start gap-4">
                                <input type="checkbox" value="{{ $document->id }}"
                                    :checked="selectedIds.includes({{ $document->id }})"
                                    @change="$event.target.checked ? selectedIds.push({{ $document->id }}) : selectedIds = selectedIds.filter(id => id !== {{ $document->id }})"
                                    class="mt-1 w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">

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
                                        {{ $document->document_number }} •
                                        Oleh {{ $document->user->name }} •
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
                                    Review →
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
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                @click.self="showConfirmModal = false" style="display: none;">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6" @click.stop>
                    <!-- Approve Confirmation -->
                    <template x-if="action === 'approve'">
                        <div>
                            <div
                                class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-success-100 dark:bg-success-900/30 rounded-full">
                                <x-icon name="check-circle" class="w-10 h-10 text-success-600" />
                            </div>
                            <h3 class="text-xl font-semibold text-center text-gray-900 dark:text-white mb-2">
                                Konfirmasi Persetujuan
                            </h3>
                            <p class="text-center text-gray-600 dark:text-gray-400 mb-2">
                                Anda yakin ingin <strong class="text-success-600">MENYETUJUI</strong>
                            </p>
                            <p class="text-center text-lg font-bold text-gray-900 dark:text-white mb-4">
                                <span x-text="selectedIds.length"></span> dokumen?
                            </p>
                            <p class="text-center text-sm text-gray-500 mb-6">
                                Tindakan ini tidak dapat dibatalkan.
                            </p>

                            <div class="flex gap-3">
                                <button type="button" @click="showConfirmModal = false"
                                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="flex-1 px-4 py-2 bg-success-600 hover:bg-success-700 text-white rounded-lg transition-colors">
                                    Ya, Setujui Semua
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
                                Konfirmasi Penolakan
                            </h3>
                            <p class="text-center text-gray-600 dark:text-gray-400 mb-2">
                                Anda yakin ingin <strong class="text-danger-600">MENOLAK</strong>
                            </p>
                            <p class="text-center text-lg font-bold text-gray-900 dark:text-white mb-4">
                                <span x-text="selectedIds.length"></span> dokumen?
                            </p>
                            <p class="text-center text-sm text-gray-500 mb-6">
                                Tindakan ini tidak dapat dibatalkan.
                            </p>

                            <div class="flex gap-3">
                                <button type="button" @click="showConfirmModal = false"
                                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="flex-1 px-4 py-2 bg-danger-600 hover:bg-danger-700 text-white rounded-lg transition-colors">
                                    Ya, Tolak Semua
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
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tidak ada dokumen menunggu approval</h3>
                <p class="mt-2 text-sm text-gray-500">Semua dokumen sudah diproses.</p>
            </div>
        </x-ui.card>
    @endif
</x-app-layout>