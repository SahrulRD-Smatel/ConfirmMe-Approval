@props(['document'])
            {{-- Dynamic Fields Details --}}
            @php
                $template = \App\Models\DocumentTemplate::where('code', $document->template)->first();
                $dynamicFields = $template ? ($template->fields['dynamic_fields'] ?? []) : [];
            @endphp

            @if(count($dynamicFields) > 0 && !empty($document->metadata))
                <x-ui.card>
                    <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Informasi Spesifik') }}</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach($dynamicFields as $field)
                            @php $fieldName = $field['name']; $fieldValue = $document->metadata[$fieldName] ?? null; @endphp
                            
                            @if($field['type'] === 'table')
                                <div class="col-span-1 sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ $field['label'] }}</dt>
                                    <dd class="mt-1">
                                        @if(is_array($fieldValue) && count($fieldValue) > 0)
                                            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                                        <tr>
                                                            @php $cols = array_map('trim', explode(',', $field['columns'])); @endphp
                                                            @foreach($cols as $col)
                                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $col }}</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                                        @foreach($fieldValue as $row)
                                                            <tr>
                                                                @foreach($cols as $col)
                                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $row[$col] ?? '-' }}</td>
                                                                @endforeach
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400 italic">{{ __('Tidak ada data') }}</span>
                                        @endif
                                    </dd>
                                </div>
                            @elseif($field['type'] === 'textarea')
                                <div class="col-span-1 sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ $field['label'] }}</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white whitespace-pre-line bg-gray-50/80 dark:bg-gray-800 p-3 rounded-xl border border-gray-100 dark:border-gray-700">
                                        {{ $fieldValue ?: '-' }}
                                    </dd>
                                </div>
                            @elseif($field['type'] === 'file' && $fieldValue)
                                <div class="col-span-1 sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ $field['label'] }}</dt>
                                    <dd>
                                        @if(in_array(strtolower(pathinfo($fieldValue, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))
                                            <a href="{{ Storage::url($fieldValue) }}" target="_blank" class="block max-w-xs overflow-hidden rounded-xl border border-gray-200 shadow-sm hover:opacity-90">
                                                <img src="{{ Storage::url($fieldValue) }}" alt="{{ $field['label'] }}" class="w-full h-auto" />
                                            </a>
                                        @else
                                            <a href="{{ Storage::url($fieldValue) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                                                <x-icon name="arrow-down-tray" class="w-4 h-4" />
                                                {{ __('Download Lampiran') }}
                                            </a>
                                        @endif
                                    </dd>
                                </div>
                            @else
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $field['label'] }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-medium">
                                        @if($fieldValue)
                                            @if($field['type'] === 'date')
                                                {{ \Carbon\Carbon::parse($fieldValue)->format('d F Y') }}
                                            @elseif($field['type'] === 'number')
                                                {{ is_numeric($fieldValue) ? number_format((float)$fieldValue, 0, ',', '.') : $fieldValue }}
                                            @else
                                                {{ $fieldValue }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                            @endif
                        @endforeach
                    </dl>
                </x-ui.card>
            @endif
