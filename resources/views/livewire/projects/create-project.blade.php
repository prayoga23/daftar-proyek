<div class="w-full">
    <div class="flex justify-between">
        <h1 class="text-3xl font-bold">Tambah Proyek Baru</h1>
        <flux:button wire:navigate href="/projects" variant="filled"><- &nbsp; &nbsp; Kembali</flux:button>
    </div>
    <hr class="my-8">
    <div class="w-full">
        <form wire:submit="save" action="" class="space-y-4">
            <flux:field>
                <flux:label>Nama Proyek</flux:label>
                <flux:textarea rows="2" wire:model="nama_proyek" type="text" placeholder="Masukkan nama proyek" />
                <flux:error name="nama_proyek" />
            </flux:field>
            <div class="space-y-3">
                <h3 class="text-lg font-medium">Owner:</h3>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <label class="text-sm font-medium text-gray-700 min-w-[60px]">Nama</label>
                        <flux:input wire:model="owner_input" type="text" placeholder="Masukkan nama owner" class="flex-1" />
                        <flux:button type="button" wire:click="addOwner" variant="outline" size="sm">
                            Masukkan
                        </flux:button>
                    </div>
                    @if(count($selected_owners) > 0)
                    <div class="flex flex-wrap gap-2 ml-[60px]">
                        @foreach($selected_owners as $index => $owner_name)
                        <div class="flex items-center bg-blue-100 text-blue-800 px-3 py-1 rounded text-sm border">
                            <span>{{ $owner_name }}</span>
                            <button type="button" wire:click="removeOwner({{ $index }})" class="ml-2 text-blue-600 hover:text-blue-800 font-bold">
                                ×
                            </button>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                <flux:error name="owner" />
            </div>
            <div class="space-y-3">
                <h3 class="text-lg font-medium">Jenis Pekerjaan</h3>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <label class="text-sm font-medium text-gray-700 min-w-[60px]">Pilih</label>
                        <flux:select wire:model="jenis_pekerjaan_selected" wire:change="addJenisPekerjaan" placeholder="Pilih jenis pekerjaan" class="flex-1">
                            <option value="">Pilih jenis pekerjaan</option>
                            <option value="PBG">PBG</option>
                            <option value="SLF">SLF</option>
                        </flux:select>
                    </div>
                    @if(count($selected_jenis_pekerjaan) > 0)
                    <div class="flex flex-wrap gap-2 ml-[60px]">
                        @foreach($selected_jenis_pekerjaan as $index => $jenis)
                        <div class="flex items-center bg-purple-100 text-purple-800 px-3 py-1 rounded text-sm border">
                            <span>{{ $jenis }}</span>
                            <button type="button" wire:click="removeJenisPekerjaan({{ $index }})" class="ml-2 text-purple-600 hover:text-purple-800 font-bold">
                                ×
                            </button>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                <flux:error name="jenis_pekerjaan" />
            </div>
            <div class="space-y-3">
                <h3 class="text-lg font-medium">Yang Mengerjakan:</h3>
                <div class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <flux:field>
                            <flux:label>
                                Job Desk 
                                @if(count($selected_jenis_pekerjaan) > 0)
                                    <span class="text-sm font-normal text-gray-600">({{ implode(', ', $selected_jenis_pekerjaan) }})</span>
                                @endif
                            </flux:label>
                            <flux:select wire:model="job_desk_input" placeholder="Pilih Job Desk">
                                <option value="">Pilih Job Desk</option>
                                @if(in_array('PBG', $selected_jenis_pekerjaan))
                                    <option value="IRK">IRK</option>
                                    <option value="Gambar Arsitek">Gambar Arsitek</option>
                                    <option value="Gambar Struktur">Gambar Struktur</option>
                                    <option value="Gambar MEP">Gambar MEP</option>
                                    <option value="SKA Arsitek">SKA Arsitek</option>
                                    <option value="SKA Struktur">SKA Struktur</option>
                                    <option value="SKA MEP">SKA MEP</option>
                                    <option value="Proses PBG">Proses PBG</option>
                                @endif
                                @if(in_array('SLF', $selected_jenis_pekerjaan))
                                    <option value="Pengukuran">Pengukuran</option>
                                    <option value="IRK">IRK</option>
                                    <option value="Gambar Arsitek">Gambar Arsitek</option>
                                    <option value="Gambar Struktur">Gambar Struktur</option>
                                    <option value="Gambar MEP">Gambar MEP</option>
                                    <option value="SKA">SKA</option>
                                    <option value="Kajian">Kajian</option>
                                    <option value="Proses SLF">Proses SLF</option>
                                @endif
                            </flux:select>
                        </flux:field>
                         <flux:field>
                            <flux:label>Nama</flux:label>
                            <flux:input wire:model="yang_mengerjakan_input" type="text" placeholder="Masukkan nama" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:button type="button" wire:click="addYangMengerjakan" variant="outline" size="sm">
                            Masukkan
                        </flux:button>
                    </div>
                    @if(count($selected_yang_mengerjakan) > 0)
                    <div class="space-y-2">
                        <p class="text-sm font-medium text-gray-700">Daftar Yang Mengerjakan:</p>
                        <div class="grid grid-cols-1 gap-2">
                            @foreach($selected_yang_mengerjakan as $index => $pekerja)
                            <div class="flex items-center justify-between bg-green-50 p-3 rounded border border-green-200">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-green-800">{{ $pekerja['nama'] }}</span>
                                        <span class="text-gray-400">•</span>
                                        <span class="text-sm bg-green-100 text-green-700 px-2 py-1 rounded">{{ $pekerja['job_desk'] }}</span>
                                    </div>
                                </div>
                                <button type="button" wire:click="removeYangMengerjakan({{ $index }})" class="ml-3 text-green-600 hover:text-green-800 font-bold text-xl">
                                    ×
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                <flux:error name="yang_mengerjakan" />
            </div>
            <flux:field>
                <flux:label>Keterangan / Status Pekerjaan</flux:label>
                <div class="space-y-3">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-700 mb-3">Pada siapa jenis pekerjaan dan Yang Mengerjakan:</h4>
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <flux:field>
                                    <flux:select wire:model="status_yang_mengerjakan" placeholder="Pilih yang mengerjakan">
                                        <option value="">Pilih yang mengerjakan</option>
                                        @if(count($selected_yang_mengerjakan) > 0)
                                            @foreach($selected_yang_mengerjakan as $pekerja)
                                            <option value="{{ $pekerja['nama'] }} ({{ $pekerja['job_desk'] }})">{{ $pekerja['nama'] }} - {{ $pekerja['job_desk'] }}</option>
                                            @endforeach
                                        @endif
                                    </flux:select>
                                </flux:field>
                            </div>

                            
                        </div>
                    </div>
                    
                    <flux:field>
                        <flux:label>Keterangan / Status Pekerjaan:</flux:label>
                        <flux:textarea rows="4" wire:model="status_pekerjaan" placeholder="Masukkan keterangan atau status pekerjaan secara detail..." />
                        <flux:error name="status_pekerjaan" />
                    </flux:field>
                    <div>
                                <flux:button type="button" wire:click="addJobDeskAssignment" variant="outline" size="sm">
                                    Masukkan
                                </flux:button>
                    </div>
                    @if(isset($job_desk_assignments) && count($job_desk_assignments) > 0)
                            <div class="mt-3 space-y-2">
                                <p class="text-sm font-medium text-gray-700">Daftar Job Desk yang Dipilih:</p>
                                <div class="space-y-2">
                                    @foreach($job_desk_assignments as $index => $assignment)
                                    <div class="bg-blue-50 p-3 rounded border border-blue-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="text-sm">
                                                <span class="font-medium text-blue-800">{{ $assignment['jenis_pekerjaan'] }}</span>
                                                <span class="text-gray-500 mx-2">→</span>
                                                <span class="text-blue-700">{{ $assignment['yang_mengerjakan'] }}</span>
                                            </div>
                                            <button type="button" wire:click="removeJobDeskAssignment({{ $index }})" class="text-blue-600 hover:text-blue-800 font-bold text-lg">
                                                ×
                                            </button>
                                        </div>
                                        <div class="text-xs text-gray-700 bg-white p-2 rounded">
                                            {{ $assignment['keterangan'] }}
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                </div>
            </flux:field>
            
            <!-- Preview Normalisasi -->
            @if(isset($job_desk_assignments) && count($job_desk_assignments) > 0)
            <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800 mb-3">📊 Preview Data yang Akan Disimpan</h3>
                <div class="text-sm text-blue-700 space-y-3">
                    <div>
                        <p class="font-medium mb-2">Nama Proyek:</p>
                        <div class="bg-white p-2 rounded border text-gray-800">
                            {{ $nama_proyek ?: 'Belum diisi' }}
                        </div>
                    </div>
                    <div>
                        <p class="font-medium mb-2">Owner:</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach($selected_owners as $owner)
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">{{ $owner }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <p class="font-medium mb-2">Total Baris yang Akan Dibuat: <span class="text-lg font-bold">{{ count($job_desk_assignments) }}</span></p>
                        <div class="bg-white rounded border overflow-hidden">
                            <table class="w-full text-xs">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-2 py-2 text-left border-b">No</th>
                                        <th class="px-2 py-2 text-left border-b">Jenis Pekerjaan</th>
                                        <th class="px-2 py-2 text-left border-b">Yang Mengerjakan</th>
                                        <th class="px-2 py-2 text-left border-b">Status Pekerjaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($job_desk_assignments as $index => $assignment)
                                    <tr class="border-b {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                        <td class="px-2 py-2">{{ $index + 1 }}</td>
                                        <td class="px-2 py-2">
                                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded">
                                                {{ $assignment['jenis_pekerjaan'] }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-2">
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded">
                                                {{ $assignment['yang_mengerjakan'] }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-2 text-gray-700">
                                            {{ Str::limit($assignment['keterangan'], 50) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <hr class="mt-12">
            <flux:button type="submit" variant="primary" class="mt-6">
                Simpan Proyek
            </flux:button>
        </form>
    </div>
</div>
