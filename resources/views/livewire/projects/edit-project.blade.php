<div class="w-full">
    <div class="flex justify-between">
        <h1 class="text-3xl font-bold">Edit Proyek</h1>
        <flux:button wire:navigate href="/projects" variant="filled"><- &nbsp; &nbsp; Kembali</flux:button>
    </div>
    <hr class="my-8">
    <div class="w-full">
        <form wire:submit="update" action="" class="space-y-4">
            @if($edit_mode !== 'row')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Kolom yang diedit</flux:label>
                    <flux:select wire:model="edit_field">
                        <option value="all">Semua Kolom</option>
                        <option value="nama_proyek">Nama Proyek</option>
                        <option value="owner">Owner</option>
                        <option value="jenis_pekerjaan">Jenis Pekerjaan</option>
                        <option value="yang_mengerjakan">Yang Mengerjakan</option>
                        <option value="status_pekerjaan">Status Pekerjaan</option>
                    </flux:select>
                </flux:field>
            </div>
            @else
            <div class="mb-2">
                <flux:badge>Mode: Edit per-baris — Kolom: <b class="ml-1">{{ str_replace('_',' ', ucfirst($edit_field)) }}</b></flux:badge>
            </div>
            @endif
            @if($this->isEditing('nama_proyek'))
            <flux:field>
                <flux:label>Nama Proyek</flux:label>
                <flux:input wire:model="nama_proyek" type="text" placeholder="Masukkan nama proyek" />
                <flux:error name="nama_proyek" />
            </flux:field>
            @endif
            @if($this->isEditing('owner'))
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
            @endif
            @if($this->isEditing('jenis_pekerjaan'))
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
                        <flux:button type="button" wire:click="addJenisPekerjaan" variant="outline" size="sm">
                            Masukkan
                        </flux:button>
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
            
            <!-- PBG Specific Fields -->
            @if(in_array('PBG', $selected_jenis_pekerjaan))
            <div class="bg-orange-50 p-6 rounded-lg border-2 border-orange-200">
                <h3 class="text-xl font-bold text-orange-800 mb-4">📋 Form PBG</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>IRK</flux:label>
                        <flux:input wire:model="irk_pbg" type="text" placeholder="Status IRK" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Gambar Arsitek</flux:label>
                        <flux:input wire:model="gambar_arsitek_pbg" type="text" placeholder="Status Gambar Arsitek" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Gambar Struktur</flux:label>
                        <flux:input wire:model="gambar_struktur_pbg" type="text" placeholder="Status Gambar Struktur" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Gambar MEP</flux:label>
                        <flux:input wire:model="gambar_mep_pbg" type="text" placeholder="Status Gambar MEP" />
                    </flux:field>
                    <flux:field>
                        <flux:label>SKA</flux:label>
                        <flux:input wire:model="ska_pbg" type="text" placeholder="Status SKA" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Proses PBG</flux:label>
                        <flux:input wire:model="proses_pbg" type="text" placeholder="Status Proses PBG" />
                    </flux:field>
                </div>
            </div>
            @endif
            
            <!-- SLF Specific Fields -->
            @if(in_array('SLF', $selected_jenis_pekerjaan))
            <div class="bg-blue-50 p-6 rounded-lg border-2 border-blue-200">
                <h3 class="text-xl font-bold text-blue-800 mb-4">📋 Form SLF</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Pengukuran</flux:label>
                        <flux:input wire:model="pengukuran_slf" type="text" placeholder="Status Pengukuran" />
                    </flux:field>
                    <flux:field>
                        <flux:label>IRK</flux:label>
                        <flux:input wire:model="irk_slf" type="text" placeholder="Status IRK" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Gambar Arsitek</flux:label>
                        <flux:input wire:model="gambar_arsitek_slf" type="text" placeholder="Status Gambar Arsitek" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Gambar Struktur</flux:label>
                        <flux:input wire:model="gambar_struktur_slf" type="text" placeholder="Status Gambar Struktur" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Gambar MEP</flux:label>
                        <flux:input wire:model="gambar_mep_slf" type="text" placeholder="Status Gambar MEP" />
                    </flux:field>
                    <flux:field>
                        <flux:label>SKA</flux:label>
                        <flux:input wire:model="ska_slf" type="text" placeholder="Status SKA" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Kajian</flux:label>
                        <flux:input wire:model="kajian_slf" type="text" placeholder="Status Kajian" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Proses SLF</flux:label>
                        <flux:input wire:model="proses_slf" type="text" placeholder="Status Proses SLF" />
                    </flux:field>
                </div>
            </div>
            @endif
            @endif
            @if($this->isEditing('yang_mengerjakan'))
            <div class="space-y-3">
                <h3 class="text-lg font-medium">Yang Mengerjakan:</h3>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <label class="text-sm font-medium text-gray-700 min-w-[60px]">Nama</label>
                        <flux:input wire:model="yang_mengerjakan_input" type="text" placeholder="Masukkan nama yang mengerjakan" class="flex-1" />
                        <flux:button type="button" wire:click="addYangMengerjakan" variant="outline" size="sm">
                            Masukkan
                        </flux:button>
                    </div>
                    @if(count($selected_yang_mengerjakan) > 0)
                    <div class="flex flex-wrap gap-2 ml-[60px]">
                        @foreach($selected_yang_mengerjakan as $index => $nama)
                        <div class="flex items-center bg-green-100 text-green-800 px-3 py-1 rounded text-sm border">
                            <span>{{ $nama }}</span>
                            <button type="button" wire:click="removeYangMengerjakan({{ $index }})" class="ml-2 text-green-600 hover:text-green-800 font-bold">
                                ×
                            </button>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                <flux:error name="yang_mengerjakan" />
            </div>
            @endif
            @if($this->isEditing('status_pekerjaan'))
            <flux:field>
                <flux:label>Keterangan / Status Pekerjaan</flux:label>
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-700 mb-3">Pada siapa jenis pekerjaan dan Yang Mengerjakan:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>Jenis Pekerjaan:</flux:label>
                                <flux:select wire:model="status_jenis_pekerjaan" placeholder="Pilih jenis pekerjaan">
                                    <option value="">Pilih jenis pekerjaan</option>
                                    @if(count($selected_jenis_pekerjaan) > 0)
                                        @foreach($selected_jenis_pekerjaan as $jenis)
                                        <option value="{{ $jenis }}">{{ $jenis }}</option>
                                        @endforeach
                                    @endif
                                </flux:select>
                            </flux:field>
                            <flux:field>
                                <flux:label>Yang Mengerjakan:</flux:label>
                                <flux:select wire:model="status_yang_mengerjakan" placeholder="Pilih yang mengerjakan">
                                    <option value="">Pilih yang mengerjakan</option>
                                    @if(count($selected_yang_mengerjakan) > 0)
                                        @foreach($selected_yang_mengerjakan as $nama)
                                        <option value="{{ $nama }}">{{ $nama }}</option>
                                        @endforeach
                                    @endif
                                </flux:select>
                            </flux:field>
                        </div>
                    </div>
                    
                    <flux:field>
                        <flux:label>Keterangan / Status Pekerjaan:</flux:label>
                        <flux:textarea rows="4" wire:model="status_pekerjaan" placeholder="Masukkan keterangan atau status pekerjaan secara detail..." />
                        <flux:error name="status_pekerjaan" />
                    </flux:field>
                </div>
            </flux:field>
            @endif
            
            <!-- Preview Normalisasi -->
            @if(($this->isEditing('owner') && count($selected_owners) > 0) || ($this->isEditing('jenis_pekerjaan') && count($selected_jenis_pekerjaan) > 0) || ($this->isEditing('yang_mengerjakan') && count($selected_yang_mengerjakan) > 0) || ($this->isEditing('status_pekerjaan') && $status_pekerjaan))
            <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800 mb-3">📊 Preview Data yang Akan Disimpan</h3>
                <div class="text-sm text-blue-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($this->isEditing('owner'))
                        <div>
                            <p class="font-medium mb-2">Owner ({{ count($selected_owners) }}):</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach($selected_owners as $owner)
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">{{ $owner }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if($this->isEditing('jenis_pekerjaan'))
                        <div>
                            <p class="font-medium mb-2">Jenis Pekerjaan ({{ count($selected_jenis_pekerjaan) }}):</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach($selected_jenis_pekerjaan as $jenis)
                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">{{ $jenis }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if($this->isEditing('yang_mengerjakan'))
                        <div>
                            <p class="font-medium mb-2">Yang Mengerjakan ({{ count($selected_yang_mengerjakan) }}):</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach($selected_yang_mengerjakan as $nama)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">{{ $nama }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if($this->isEditing('status_pekerjaan'))
                        <div>
                            <p class="font-medium mb-2">Status Pekerjaan:</p>
                            @if($status_pekerjaan)
                            <div class="bg-white p-2 rounded border text-xs">
                                {{ Str::limit($status_pekerjaan, 100) }}
                            </div>
                            @else
                            <span class="text-gray-500 text-xs">Belum ada status</span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
            <hr class="mt-12">
            <flux:button type="submit" variant="primary" class="mt-6">
                Update Proyek
            </flux:button>
        </form>
    </div>
</div>
