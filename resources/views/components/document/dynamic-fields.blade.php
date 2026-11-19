@props(['document'])
            {{-- Dynamic Fields Details --}}
            @php
                $template = $document->documentTemplate ?? ($document->template ? \App\Models\DocumentTemplate::where('code', $document->template)->first() : null);
                $dynamicFields = $template ? ($template->fields['dynamic_fields'] ?? []) : [];
            @endphp

            @if(count($dynamicFields) > 0 && !empty($document->metadata))
                <x-ui.card>
                    <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Informasi Spesifik') }}</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach($dynamicFields as $field)
                            @php $fieldName = $field['name']; $fieldValue = $document->metadata[$fieldName] ?? null; @endphp
                            
                            @if($field['type'] === 'section')
                                <div class="col-span-1 sm:col-span-2 pt-3 pb-1 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 tracking-wide">{{ $field['label'] }}</h3>
                                </div>
                            @elseif($field['type'] === 'table')
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
                                                                @php
                                                                    $isColNum = (bool)preg_match('/harga|biaya|nominal|tarif|price|rp|qty|jumlah|kuantitas|total|subtotal|pagu|anggaran/i', $col);
                                                                @endphp
                                                                <th scope="col" class="px-4 py-3 {{ $isColNum ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $col }}</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                                        @php
                                                            $colSums = [];
                                                            $hasColSums = false;
                                                            foreach ($cols as $c) {
                                                                if (preg_match('/harga|biaya|nominal|tarif|price|rp|total|subtotal/i', $c)) {
                                                                    $colSums[$c] = 0;
                                                                    $hasColSums = true;
                                                                } else {
                                                                    $colSums[$c] = null;
                                                                }
                                                            }
                                                        @endphp
                                                        @foreach($fieldValue as $row)
                                                            <tr>
                                                                @foreach($cols as $col)
                                                                    @php
                                                                        $cellVal = $row[$col] ?? '-';
                                                                        $isColNum = (bool)preg_match('/harga|biaya|nominal|tarif|price|rp|qty|jumlah|kuantitas|total|subtotal|pagu|anggaran/i', $col);
                                                                        $isValNum = ($cellVal !== '-' && $cellVal !== '' && is_numeric(str_replace(['.', ',', ' '], '', (string)$cellVal)));

                                                                        if ($isValNum && preg_match('/harga|biaya|nominal|tarif|price|rp|total|subtotal/i', $col)) {
                                                                            $rawNum = (float)str_replace(['.', ','], '', (string)$cellVal);
                                                                            if (isset($colSums[$col])) {
                                                                                $colSums[$col] += $rawNum;
                                                                            }
                                                                            $cellVal = 'Rp ' . number_format($rawNum, 0, ',', '.');
                                                                        } elseif ($isValNum && preg_match('/qty|jumlah|kuantitas/i', $col)) {
                                                                            $cellVal = number_format((float)str_replace(['.', ','], '', (string)$cellVal), 0, ',', '.');
                                                                        }
                                                                    @endphp
                                                                    <td class="px-4 py-3 whitespace-nowrap text-sm {{ ($isColNum || $isValNum) ? 'text-right font-mono' : 'text-left' }} text-gray-900 dark:text-white">{{ $cellVal }}</td>
                                                                @endforeach
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    @if($hasColSums)
                                                        <tfoot class="bg-gray-100 dark:bg-gray-800 font-bold border-t-2 border-gray-200 dark:border-gray-700">
                                                            <tr>
                                                                @foreach($cols as $cIdx => $col)
                                                                    <td class="px-4 py-3 whitespace-nowrap text-sm {{ $colSums[$col] !== null ? 'text-right font-mono text-gray-900 dark:text-white' : 'text-left text-xs uppercase tracking-wider text-gray-600 dark:text-gray-300' }}">
                                                                        @if($colSums[$col] !== null)
                                                                            Rp {{ number_format($colSums[$col], 0, ',', '.') }}
                                                                        @elseif($cIdx === 0)
                                                                            {{ __('Total') }}
                                                                        @endif
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                        </tfoot>
                                                    @endif
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
                                @php
                                    $isTotalField = (bool)preg_match('/(?:^total|total_|_total|jumlah_total|grand_total|biaya_total|total_estimasi|estimasi_total)/i', $field['name']) || (bool)preg_match('/(?:^total|\btotal\b|grand total|jumlah total)/i', $field['label']);
                                @endphp
                                <div class="{{ $isTotalField ? 'col-span-1 sm:col-span-2 p-4 rounded-xl border border-primary-200/80 dark:border-primary-900/50 bg-primary-50/40 dark:bg-primary-950/20' : '' }}">
                                    <dt class="text-sm font-medium {{ $isTotalField ? 'text-primary-900 dark:text-primary-300 font-bold uppercase tracking-wide text-xs flex items-center justify-between' : 'text-gray-500 dark:text-gray-400' }}">
                                        <span>{{ $field['label'] }}</span>
                                        @if($isTotalField)
                                            <span class="inline-flex items-center gap-1 text-[10px] bg-primary-100 text-primary-800 dark:bg-primary-900/60 dark:text-primary-300 px-2 py-0.5 rounded font-semibold">{{ __('Auto-Sum') }}</span>
                                        @endif
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-medium {{ $isTotalField ? 'text-lg font-bold text-primary-900 dark:text-primary-100 font-mono' : '' }}">
                                        @if($fieldValue)
                                            @if($field['type'] === 'date')
                                                {{ \Carbon\Carbon::parse($fieldValue)->translatedFormat('d F Y') }}
                                            @elseif($field['type'] === 'time')
                                                {{ (strpos($fieldValue, 'WIB') !== false) ? $fieldValue : $fieldValue . ' WIB' }}
                                            @elseif($field['type'] === 'number')
                                                {{ $isTotalField ? 'Rp ' : '' }}{{ is_numeric($fieldValue) ? number_format((float)$fieldValue, 0, ',', '.') : $fieldValue }}
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
