<div class="w-full">
    <div class="flex justify-between">
        <h1 class="text-3xl font-bold">Daftar Kerja</h1>
        <div class="flex gap-3">
            <!-- <button id="toggle-normalization" class="btn-normalize" onclick="dataNormalizer.toggleNormalization()">
                
            </button> -->
            <flux:button wire:navigate href="/projects/create" variant="primary">Tambah Proyek</flux:button>
        </div>
    </div>
    <hr class="mt-7 mb-9">
    <div class="w-full">
        <div class="flex w-xl mb-8">
            <!-- Used debounce and input event to trigger search on every keystroke -->
            <flux:input 
                wire:model.debounce.1ms="textInput" 
                wire:input="search"
                placeholder="Cari Daftar kerja..." 
                class="w-sm me-2" 
            />
        </div>
        <div class="space-x-2">
            <!-- Search Badge: Appears only when there is a search term -->
            @if ($textInput)
                <flux:badge class="mb-5">
                    Pencarian: <span class="ms-1"><b>{{ $textInput }}</b></span>
                    <flux:badge.close wire:click="clearSearch" class="cursor-pointer" />
                </flux:badge>
            @endif
            <!-- Sorting Badge: Displays if sorting is applied -->
            @if ($sortColumn && $sortDirection)
                <flux:badge class="mb-5">
                    Urutkan: <span class="ms-1 me-2"><b>{{ ucfirst($sortColumn) }}</b>,</span> Arah: <span class="ms-1 me-2"><b>{{ ucfirst($sortDirection) }}</b></span>
                    <flux:badge.close wire:click="resetSorting" class="cursor-pointer" />
                </flux:badge>
            @endif
            <!-- "Clear All" Badge: Displayed when both search and sorting are active -->
            @if ($textInput && $sortColumn && $sortDirection)
                <flux:badge class="mb-5">
                    Hapus Semua
                    <flux:badge.close wire:click="clearAll" class="cursor-pointer" />
                </flux:badge>
            @endif
        </div>
        <x-message></x-message>
        
        <!-- Info tentang normalisasi data -->
        <div id="normalization-info" class="normalization-info" style="display: none;">
            Data telah dinormalisasi. Setiap jenis pekerjaan ditampilkan dalam baris terpisah untuk analisis yang lebih baik.
        </div>
        
        <div class="normalized-table-container">
            <!-- Desktop Table View -->
            <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-300">
            <table class="min-w-full border-collapse border border-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-300" style="width: 40px;"></th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors border border-gray-300" wire:click="sortBy('id')">
                            <div class="flex items-center gap-1">
                                NO.
                                @if ($sortColumn === 'id')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('nama_proyek')" class="px-4 py-3 text-left text-xs font-extrabold text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors border border-gray-300">
                            <div class="flex items-center gap-1">
                                PROYEK
                                @if ($sortColumn === 'nama_proyek')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('owner')" class="px-4 py-3 text-left text-xs font-extrabold text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors border border-gray-300">
                            <div class="flex items-center gap-1">
                                OWNER
                                @if ($sortColumn === 'owner')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('jenis_pekerjaan')" class="px-4 py-3 text-left text-xs font-extrabold text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors border border-gray-300">
                            <div class="flex items-center gap-1">
                                JENIS PEKERJAAN
                                @if ($sortColumn === 'jenis_pekerjaan')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('yang_mengerjakan')" class="px-4 py-3 text-left text-xs font-extrabold text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors border border-gray-300">
                            <div class="flex items-center gap-1">
                                YANG MENGERJAKAN
                                @if ($sortColumn === 'yang_mengerjakan')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('status_pekerjaan')" class="px-4 py-3 text-left text-xs font-extrabold text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors border border-gray-300">
                            <div class="flex items-center gap-1">
                                STATUS
                                @if ($sortColumn === 'status_pekerjaan')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-extrabold text-gray-700 uppercase tracking-wider border border-gray-300">Action</th>
                    </tr>
                    <tr class="bg-blue-50 border-t border-gray-300">
                        <td colspan="8" class="px-4 py-2 text-center text-sm font-semibold text-blue-600 border border-gray-300">
                            Periode 2022 - 2023
                        </td>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if($projects->isNotEmpty())
                        @php
                            $groupedProjects = $projects->groupBy('nama_proyek');
                        @endphp
                        
                        @foreach ($groupedProjects as $projectName => $projectGroup)
                            @php
                                // Calculate row number based on group offset from previous pages
                                $rowNumber = $groupOffset + $loop->iteration;
                                
                                $firstProject = $projectGroup->first();
                                $totalRows = $projectGroup->count();
                                
                                // Group by owner untuk struktur bercabang yang lebih baik
                                $groupedByOwner = $projectGroup->groupBy('owner');
                                $ownerRowCounts = [];
                                foreach ($groupedByOwner as $owner => $ownerProjects) {
                                    $ownerRowCounts[$owner] = $ownerProjects->count();
                                }
                            @endphp
                            
                            @foreach ($groupedByOwner as $owner => $ownerProjects)
                                @foreach ($ownerProjects as $index => $project)
                                    @php
                                        // Calculate global row index across all owners in this project
                                        $globalRowIndex = 0;
                                        foreach ($groupedByOwner as $oKey => $oProjects) {
                                            if ($oKey === $owner) {
                                                $globalRowIndex += $index;
                                                break;
                                            }
                                            $globalRowIndex += $oProjects->count();
                                        }
                                    @endphp
                                    <tr class="{{ $project->status_pekerjaan === 'Selesai' ? 'bg-green-200' : (in_array($project->id, $highlightedProjects) ? 'bg-yellow-200' : ($globalRowIndex % 2 == 0 ? 'bg-white' : 'bg-gray-50')) }} hover:bg-blue-50 transition-colors hover-row" data-project-id="{{ $project->id }}">
                                        <td class="px-3 py-4 whitespace-nowrap align-middle relative border border-gray-300">
                                            <div class="flex flex-col gap-1 items-center action-buttons-container">
                                            
                                                <flux:button wire:click="showInsertRow({{ $project->id }})" size="xs" variant="ghost" class="add-row-btn" title="Tambah baris baru">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                                    </svg>
                                                </flux:button>
                                            </div>
                                        </td>
                                        @if($index === 0 && $owner === $groupedByOwner->keys()->first())
                                            <td class="px-4 py-4 whitespace-nowrap align-top border border-gray-300" rowspan="{{ $totalRows }}">
                                                <div class="font-bold text-center text-lg text-gray-900">{{ $rowNumber }}</div>
                                            </td>
                                            <td class="px-4 py-4 align-top border border-gray-300" rowspan="{{ $totalRows }}">
                                                <div class="font-semibold text-gray-900 text-sm">{!! str_replace('Jl. ', '<br>Jl. ', $project->nama_proyek) !!}</div>
                                            </td>
                                        @endif
                                        
                                        @if($index === 0)
                                            <td class="px-4 py-4 whitespace-nowrap align-top border border-gray-300" rowspan="{{ $ownerRowCounts[$owner] }}">
                                                <div class="flex items-center gap-2">
                                                    <div class="font-semibold text-blue-900 bg-blue-200 px-3 py-1.5 rounded-md border border-blue-300">
                                                        {{ $project->owner }}
                                                    </div>
                                                    <flux:button wire:navigate href="/projects/{{ $project->id }}/edit?mode=row&field=owner" size="xs" variant="ghost" title="Ubah">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none">
                                                            <path d="M15.2141 5.98239L16.6158 4.58063C17.39 3.80646 18.6452 3.80646 19.4194 4.58063C20.1935 5.3548 20.1935 6.60998 19.4194 7.38415L18.0176 8.78591M15.2141 5.98239L6.98023 14.2163C5.93493 15.2616 5.41226 15.7842 5.05637 16.4211C4.70047 17.058 4.3424 18.5619 4 20C5.43809 19.6576 6.94199 19.2995 7.57889 18.9436C8.21579 18.5877 8.73844 18.0651 9.78375 17.0198L18.0176 8.78591M15.2141 5.98239L18.0176 8.78591" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                            <path d="M11 20H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                        </svg>
                                                    </flux:button>
                                                </div>
                                            </td>
                                        @endif
                                        
                                        <td class="px-4 py-4 whitespace-nowrap align-top border border-gray-300">
                                            <div class="flex items-center gap-2">
                                                <div class="font-medium text-purple-900 bg-purple-200 px-3 py-1.5 rounded-md inline-block border border-purple-300">
                                                    {{ $project->jenis_pekerjaan }}
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td class="px-4 py-4 whitespace-nowrap align-top border border-gray-300">
                                            <div class="flex items-center gap-2">
                                                <div class="font-medium text-green-900 bg-green-200 px-3 py-1.5 rounded-md inline-block border border-green-300">
                                                    {{ $project->yang_mengerjakan }}
                                                </div>
                                                <flux:button wire:navigate href="/projects/{{ $project->id }}/edit?mode=row&field=yang_mengerjakan" size="xs" variant="ghost" title="Ubah">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none">
                                                        <path d="M15.2141 5.98239L16.6158 4.58063C17.39 3.80646 18.6452 3.80646 19.4194 4.58063C20.1935 5.3548 20.1935 6.60998 19.4194 7.38415L18.0176 8.78591M15.2141 5.98239L6.98023 14.2163C5.93493 15.2616 5.41226 15.7842 5.05637 16.4211C4.70047 17.058 4.3424 18.5619 4 20C5.43809 19.6576 6.94199 19.2995 7.57889 18.9436C8.21579 18.5877 8.73844 18.0651 9.78375 17.0198L18.0176 8.78591M15.2141 5.98239L18.0176 8.78591" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M11 20H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                    </svg>
                                                </flux:button>
                                            </div>
                                        </td>
                                        
                                        <td class="px-4 py-4 whitespace-nowrap align-top border border-gray-300">
                                            <div class="flex items-center gap-2">
                                                <div class="text-sm font-medium text-gray-900 bg-gray-200 px-3 py-1.5 rounded-md border border-gray-300">
                                                    {{ $project->status_pekerjaan }}
                                                </div>
                                                <flux:button wire:navigate href="/projects/{{ $project->id }}/edit?mode=row&field=status_pekerjaan" size="xs" variant="ghost" title="Ubah">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none">
                                                        <path d="M15.2141 5.98239L16.6158 4.58063C17.39 3.80646 18.6452 3.80646 19.4194 4.58063C20.1935 5.3548 20.1935 6.60998 19.4194 7.38415L18.0176 8.78591M15.2141 5.98239L6.98023 14.2163C5.93493 15.2616 5.41226 15.7842 5.05637 16.4211C4.70047 17.058 4.3424 18.5619 4 20C5.43809 19.6576 6.94199 19.2995 7.57889 18.9436C8.21579 18.5877 8.73844 18.0651 9.78375 17.0198L18.0176 8.78591M15.2141 5.98239L18.0176 8.78591" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M11 20H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                    </svg>
                                                </flux:button>
                                            </div>
                                        </td>
                                        
                                        @if($index === 0 && $owner === $groupedByOwner->keys()->first())
                                            <td class="px-4 py-4 whitespace-nowrap align-middle border border-gray-300" rowspan="{{ $totalRows }}">
                                                <div class="flex flex-col gap-2">
                                                    <flux:button wire:confirm="Tandai proyek {{ $project->nama_proyek }} sebagai selesai?" wire:click="markAsCompleted({{ $project->id }})" size="sm" variant="primary" class="bg-green-600 hover:bg-green-700" title="Tandai Selesai">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none">
                                                            <path d="M5 14L8.5 17.5L19 6.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </flux:button>
                                                    
                                                    <flux:button 
                                                        wire:click="toggleHighlight({{ $project->id }})" 
                                                        size="sm" 
                                                        class="{{ in_array($project->id, $highlightedProjects) ? 'bg-orange-600 hover:bg-orange-700 text-white border-orange-600' : 'bg-white border-2 border-orange-600 text-orange-600 hover:bg-orange-50' }}"
                                                        title="{{ in_array($project->id, $highlightedProjects) ? 'Batalkan Highlight' : 'Tandai sebagai Proyek Mau Berjalan' }}">
                                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="{{ in_array($project->id, $highlightedProjects) ? '#EAB308' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                                    </svg>
                                                    </flux:button>
                                                    
                                                    <flux:button wire:confirm="Apakah Anda yakin ingin menghapus proyek: {{ $project->nama_proyek }}?" wire:click="delete({{ $project->id }})" size="sm" variant="danger" title="Hapus">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none">
                                                            <path d="M19.5 5.5L18.8803 15.5251C18.7219 18.0864 18.6428 19.3671 18.0008 20.2879C17.6833 20.7431 17.2747 21.1273 16.8007 21.416C15.8421 22 14.559 22 11.9927 22C9.42312 22 8.1383 22 7.17905 21.4149C6.7048 21.1257 6.296 20.7408 5.97868 20.2848C5.33688 19.3626 5.25945 18.0801 5.10461 15.5152L4.5 5.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                            <path d="M3 5.5H21M16.0557 5.5L15.3731 4.09173C14.9196 3.15626 14.6928 2.68852 14.3017 2.39681C14.215 2.3321 14.1231 2.27454 14.027 2.2247C13.5939 2 13.0741 2 12.0345 2C10.9688 2 10.436 2 9.99568 2.23412C9.8981 2.28601 9.80498 2.3459 9.71729 2.41317C9.32164 2.7167 9.10063 3.20155 8.65861 4.17126L8.05292 5.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                            <path d="M9.5 16.5L9.5 10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                            <path d="M14.5 16.5L14.5 10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                        </svg>
                                                    </flux:button>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                    
                                    <!-- Inline Insert Form Row -->
                                    @if($insertingAfterRowId === $project->id)
                                        <tr class="bg-blue-50">
                                            <td colspan="8" class="px-6 py-4 border border-gray-300">
                                                <form wire:submit.prevent="saveNewRow" class="space-y-4">
                                                    <div class="text-sm font-semibold text-blue-800 mb-3">Tambah Baris Baru</div>
                                                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                                        <div>
                                                            <flux:label>Nama Proyek</flux:label>
                                                            <flux:input wire:model="newRow.nama_proyek" type="text" placeholder="Nama proyek" />
                                                            @error('newRow.nama_proyek') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                        </div>
                                                        <div>
                                                            <flux:label>Owner</flux:label>
                                                            <flux:input wire:model="newRow.owner" type="text" placeholder="Owner" />
                                                            @error('newRow.owner') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                        </div>
                                                        <div>
                                                            <flux:label>Jenis Pekerjaan</flux:label>
                                                            <flux:input wire:model="newRow.jenis_pekerjaan" type="text" placeholder="Jenis pekerjaan" />
                                                            @error('newRow.jenis_pekerjaan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                        </div>
                                                        <div>
                                                            <flux:label>Yang Mengerjakan</flux:label>
                                                            <flux:input wire:model="newRow.yang_mengerjakan" type="text" placeholder="Yang mengerjakan" />
                                                            @error('newRow.yang_mengerjakan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                        </div>
                                                        <div>
                                                            <flux:label>Status Pekerjaan</flux:label>
                                                            <flux:input wire:model="newRow.status_pekerjaan" type="text" placeholder="Status pekerjaan" />
                                                            @error('newRow.status_pekerjaan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="flex gap-2 mt-4">
                                                        <flux:button type="submit" variant="primary" size="sm">Simpan</flux:button>
                                                        <flux:button type="button" wire:click="cancelInsert" variant="ghost" size="sm">Batal</flux:button>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endforeach
                        @endforeach
                    @else
                        <tr>
                            <td class="px-6 py-8 text-center text-gray-500 text-sm border border-gray-300" colspan="8">Tidak ada proyek ditemukan.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            </div>
            
            <!-- Mobile Card View -->
            <div class="hidden space-y-4">
                @if($projects->isNotEmpty())
                    @php
                        $groupedProjects = $projects->groupBy('nama_proyek');
                    @endphp
                    
                    @foreach ($groupedProjects as $projectName => $projectGroup)
                        @php
                            $rowNumber = $groupOffset + $loop->iteration;
                            $firstProject = $projectGroup->first();
                            $groupedByOwner = $projectGroup->groupBy('owner');
                        @endphp
                        
                        <div class="rounded-lg shadow-md overflow-hidden {{ $firstProject->status_pekerjaan === 'Selesai' ? 'bg-green-200' : (in_array($firstProject->id, $highlightedProjects) ? 'bg-yellow-200' : 'bg-white') }}">
                            <!-- Project Header -->
                            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-3">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="text-xs font-semibold text-blue-100 uppercase mb-1">Proyek #{{ $rowNumber }}</div>
                                        <div class="text-white font-bold text-base">{!! str_replace('Jl. ', 'Jl. ', $firstProject->nama_proyek) !!}</div>
                                    </div>
                                    <div class="ml-3">
                                        <flux:button wire:click="toggleHighlight({{ $firstProject->id }})" size="xs" variant="ghost" class="bg-white/20 hover:bg-white/30" title="Proyek Mau Berjalan">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="{{ in_array($firstProject->id, $highlightedProjects) ? '#EAB308' : 'none' }}" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                            </svg>
                                        </flux:button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Project Details -->
                            <div class="divide-y divide-gray-200">
                                @foreach ($groupedByOwner as $owner => $ownerProjects)
                                    <div class="p-4 bg-gray-50">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-semibold text-gray-500 uppercase">Owner:</span>
                                                <span class="font-semibold text-blue-900 bg-blue-200 px-3 py-1 rounded-full text-sm border border-blue-300">{{ $owner }}</span>
                                            </div>
                                            <flux:button wire:navigate href="/projects/{{ $ownerProjects->first()->id }}/edit?mode=row&field=owner" size="xs" variant="ghost" title="Ubah">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none">
                                                    <path d="M15.2141 5.98239L16.6158 4.58063C17.39 3.80646 18.6452 3.80646 19.4194 4.58063C20.1935 5.3548 20.1935 6.60998 19.4194 7.38415L18.0176 8.78591M15.2141 5.98239L6.98023 14.2163C5.93493 15.2616 5.41226 15.7842 5.05637 16.4211C4.70047 17.058 4.3424 18.5619 4 20C5.43809 19.6576 6.94199 19.2995 7.57889 18.9436C8.21579 18.5877 8.73844 18.0651 9.78375 17.0198L18.0176 8.78591M15.2141 5.98239L18.0176 8.78591" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M11 20H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                </svg>
                                            </flux:button>
                                        </div>
                                        
                                        <div class="space-y-3">
                                            @foreach ($ownerProjects as $project)
                                                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                                                    <div class="space-y-2">
                                                        <div>
                                                            <div class="text-xs font-semibold text-gray-500 uppercase mb-1">Jenis Pekerjaan</div>
                                                            <div class="font-medium text-purple-900 bg-purple-200 px-3 py-1.5 rounded-md text-sm inline-block border border-purple-300">{{ $project->jenis_pekerjaan }}</div>
                                                        </div>
                                                        <div>
                                                            <div class="text-xs font-semibold text-gray-500 uppercase mb-1">Yang Mengerjakan</div>
                                                            <div class="flex items-center gap-1">
                                                                <div class="font-medium text-green-900 bg-green-200 px-3 py-1.5 rounded-md text-sm inline-block border border-green-300">{{ $project->yang_mengerjakan }}</div>
                                                                <flux:button wire:navigate href="/projects/{{ $project->id }}/edit?mode=row&field=yang_mengerjakan" size="xs" variant="ghost" title="Ubah">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="12" height="12" fill="none">
                                                                        <path d="M15.2141 5.98239L16.6158 4.58063C17.39 3.80646 18.6452 3.80646 19.4194 4.58063C20.1935 5.3548 20.1935 6.60998 19.4194 7.38415L18.0176 8.78591M15.2141 5.98239L6.98023 14.2163C5.93493 15.2616 5.41226 15.7842 5.05637 16.4211C4.70047 17.058 4.3424 18.5619 4 20C5.43809 19.6576 6.94199 19.2995 7.57889 18.9436C8.21579 18.5877 8.73844 18.0651 9.78375 17.0198L18.0176 8.78591M15.2141 5.98239L18.0176 8.78591" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                                        <path d="M11 20H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                                    </svg>
                                                                </flux:button>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="text-xs font-semibold text-gray-500 uppercase mb-1">Status</div>
                                                            <div class="flex items-center gap-1">
                                                                <div class="text-sm font-medium text-gray-900 bg-gray-200 px-3 py-1.5 rounded-md border border-gray-300">{{ $project->status_pekerjaan }}</div>
                                                                <flux:button wire:navigate href="/projects/{{ $project->id }}/edit?mode=row&field=status_pekerjaan" size="xs" variant="ghost" title="Ubah">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="12" height="12" fill="none">
                                                                        <path d="M15.2141 5.98239L16.6158 4.58063C17.39 3.80646 18.6452 3.80646 19.4194 4.58063C20.1935 5.3548 20.1935 6.60998 19.4194 7.38415L18.0176 8.78591M15.2141 5.98239L6.98023 14.2163C5.93493 15.2616 5.41226 15.7842 5.05637 16.4211C4.70047 17.058 4.3424 18.5619 4 20C5.43809 19.6576 6.94199 19.2995 7.57889 18.9436C8.21579 18.5877 8.73844 18.0651 9.78375 17.0198L18.0176 8.78591M15.2141 5.98239L18.0176 8.78591" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                                        <path d="M11 20H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                                    </svg>
                                                                </flux:button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    @if($insertingAfterRowId === $project->id)
                                                        <div class="mt-3 p-3 bg-blue-50 rounded-lg border-2 border-blue-300">
                                                            <form wire:submit.prevent="saveNewRow" class="space-y-3">
                                                                <div class="text-xs font-semibold text-blue-800 mb-2">Tambah Baris Baru</div>
                                                                <div>
                                                                    <flux:label class="text-xs">Nama Proyek</flux:label>
                                                                    <flux:input wire:model="newRow.nama_proyek" type="text" placeholder="Nama proyek" class="text-sm" />
                                                                    @error('newRow.nama_proyek') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                                </div>
                                                                <div>
                                                                    <flux:label class="text-xs">Owner</flux:label>
                                                                    <flux:input wire:model="newRow.owner" type="text" placeholder="Owner" class="text-sm" />
                                                                    @error('newRow.owner') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                                </div>
                                                                <div>
                                                                    <flux:label class="text-xs">Jenis Pekerjaan</flux:label>
                                                                    <flux:input wire:model="newRow.jenis_pekerjaan" type="text" placeholder="Jenis pekerjaan" class="text-sm" />
                                                                    @error('newRow.jenis_pekerjaan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                                </div>
                                                                <div>
                                                                    <flux:label class="text-xs">Yang Mengerjakan</flux:label>
                                                                    <flux:input wire:model="newRow.yang_mengerjakan" type="text" placeholder="Yang mengerjakan" class="text-sm" />
                                                                    @error('newRow.yang_mengerjakan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                                </div>
                                                                <div>
                                                                    <flux:label class="text-xs">Status Pekerjaan</flux:label>
                                                                    <flux:input wire:model="newRow.status_pekerjaan" type="text" placeholder="Status pekerjaan" class="text-sm" />
                                                                    @error('newRow.status_pekerjaan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                                </div>
                                                                <div class="flex gap-2">
                                                                    <flux:button type="submit" variant="primary" size="sm" class="flex-1">Simpan</flux:button>
                                                                    <flux:button type="button" wire:click="cancelInsert" variant="ghost" size="sm" class="flex-1">Batal</flux:button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Project Actions -->
                            <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                                <div class="flex gap-2">
                                    <flux:button wire:confirm="Tandai proyek {{ $firstProject->nama_proyek }} sebagai selesai?" wire:click="markAsCompleted({{ $firstProject->id }})" size="sm" variant="primary" class="bg-green-600 hover:bg-green-700 flex-1" title="Tandai Selesai">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" class="inline mr-1">
                                            <path d="M5 14L8.5 17.5L19 6.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Selesai
                                    </flux:button>
                                    <flux:button wire:confirm="Apakah Anda yakin ingin menghapus proyek: {{ $firstProject->nama_proyek }}?" wire:click="delete({{ $firstProject->id }})" size="sm" variant="danger" class="flex-1" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" class="inline mr-1">
                                            <path d="M19.5 5.5L18.8803 15.5251C18.7219 18.0864 18.6428 19.3671 18.0008 20.2879C17.6833 20.7431 17.2747 21.1273 16.8007 21.416C15.8421 22 14.559 22 11.9927 22C9.42312 22 8.1383 22 7.17905 21.4149C6.7048 21.1257 6.296 20.7408 5.97868 20.2848C5.33688 19.3626 5.25945 18.0801 5.10461 15.5152L4.5 5.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                            <path d="M3 5.5H21M16.0557 5.5L15.3731 4.09173C14.9196 3.15626 14.6928 2.68852 14.3017 2.39681C14.215 2.3321 14.1231 2.27454 14.027 2.2247C13.5939 2 13.0741 2 12.0345 2C10.9688 2 10.436 2 9.99568 2.23412C9.8981 2.28601 9.80498 2.3459 9.71729 2.41317C9.32164 2.7167 9.10063 3.20155 8.65861 4.17126L8.05292 5.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                            <path d="M9.5 16.5L9.5 10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                            <path d="M14.5 16.5L14.5 10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                        </svg>
                                        Hapus
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="bg-white rounded-lg shadow-md p-8 text-center">
                        <p class="text-gray-500">Tidak ada proyek ditemukan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Include CSS dan JavaScript untuk normalisasi data -->
@push('styles')
<link rel="stylesheet" href="{{ asset('css/normalized-table.css') }}">
<style>
    .hover-row .action-buttons-container .add-row-btn {
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    
    .hover-row:hover .action-buttons-container .add-row-btn {
        opacity: 1;
    }
    
    .add-row-btn {
        padding: 4px !important;
        min-width: 24px !important;
        height: 24px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 4px !important;
        background-color: #f3f4f6 !important;
        color: #374151 !important;
    }
    
    .add-row-btn:hover {
        background-color: #e5e7eb !important;
        color: #111827 !important;
    }
    
    .highlight-btn {
        padding: 4px !important;
        min-width: 24px !important;
        height: 24px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 4px !important;
        background-color: transparent !important;
        color: #9CA3AF !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }
    
    .highlight-btn:hover {
        background-color: #FEF3C7 !important;
        color: #EAB308 !important;
    }
    
    .highlight-btn.active {
        background-color: #FEF3C7 !important;
        color: #EAB308 !important;
    }
    
    .action-buttons-container {
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/data-normalizer.js') }}"></script>
<script>
    // Update info panel saat normalisasi
    document.addEventListener('DOMContentLoaded', function() {
        const infoPanel = document.getElementById('normalization-info');
        const toggleButton = document.getElementById('toggle-normalization');
        
        // Override toggle function untuk menampilkan info
        const originalToggle = window.dataNormalizer.toggleNormalization;
        window.dataNormalizer.toggleNormalization = function() {
            originalToggle.call(this);
            
            // Toggle info panel
            if (infoPanel.style.display === 'none') {
                infoPanel.style.display = 'flex';
                toggleButton.classList.add('btn-reset');
            } else {
                infoPanel.style.display = 'none';
                toggleButton.classList.remove('btn-reset');
            }
        };
    });
</script>
@endpush
