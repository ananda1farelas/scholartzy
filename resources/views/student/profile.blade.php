@extends('layouts.dashboard')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ tab: 'profile' }">

    <!-- Tabs -->
    <div class="flex gap-1 bg-slate-900 border border-slate-800 rounded-xl p-1">
        <button @click="tab = 'profile'" :class="tab === 'profile' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white'" 
            class="flex-1 py-2.5 rounded-lg text-sm font-medium transition">
            <i class="fas fa-user mr-2"></i>Data Mahasiswa
        </button>
        <button @click="tab = 'parent'" :class="tab === 'parent' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white'" 
            class="flex-1 py-2.5 rounded-lg text-sm font-medium transition">
            <i class="fas fa-users mr-2"></i>Orang Tua / Wali
        </button>
    </div>

    <!-- Profile Form -->
    <div x-show="tab === 'profile'" x-transition>
        <form method="POST" action="{{ route('student.profile.update') }}" class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
            @csrf
            <h3 class="text-lg font-semibold mb-4">Data Mahasiswa</h3>
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-1">NIM</label>
                    <input type="text" name="student_number" value="{{ old('student_number', $student?->student_number) }}" 
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white" required>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Nama Lengkap</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $student?->full_name) }}" 
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white" required>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $student?->birth_date?->format('Y-m-d')) }}" 
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white" required>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Jenis Kelamin</label>
                    <select name="gender" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white" required>
                        <option value="">Pilih</option>
                        <option value="male" {{ old('gender', $student?->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender', $student?->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-1">No. Telepon</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $student?->phone_number) }}" 
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white" required>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Program Studi</label>
                    <input type="text" name="study_program" value="{{ old('study_program', $student?->study_program) }}" 
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white" required>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Semester</label>
                    <input type="number" name="semester" value="{{ old('semester', $student?->semester) }}" min="1" max="14"
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white" required>
                </div>
            </div>
            <div>
                <label class="block text-gray-400 text-sm mb-1">Alamat Lengkap</label>
                <textarea name="address" rows="3" 
                    class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white" required>{{ old('address', $student?->address) }}</textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition">
                    <i class="fas fa-save mr-2"></i>Simpan Profil
                </button>
            </div>
        </form>
    </div>

    <!-- Parent Form -->
    <div x-show="tab === 'parent'" x-transition>
        <form method="POST" action="{{ route('student.parent.update') }}" class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-6">
            @csrf
            
            <!-- Ayah -->
            <div>
                <h4 class="text-md font-semibold text-blue-400 mb-3"><i class="fas fa-male mr-2"></i>Data Ayah</h4>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Nama Ayah</label>
                        <input type="text" name="father_name" value="{{ old('father_name', $parentGuardian?->father_name) }}" 
                            class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Pekerjaan</label>
                        <input type="text" name="father_occupation" value="{{ old('father_occupation', $parentGuardian?->father_occupation) }}" 
                            class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Penghasilan / Bulan</label>
                        <input type="number" name="father_income" value="{{ old('father_income', $parentGuardian?->father_income) }}" 
                            class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white" placeholder="Rp">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">No. Telepon</label>
                        <input type="text" name="father_phone_number" value="{{ old('father_phone_number', $parentGuardian?->father_phone_number) }}" 
                            class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-400 text-sm mb-1">Alamat</label>
                        <textarea name="father_address" rows="2" 
                            class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white">{{ old('father_address', $parentGuardian?->father_address) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Ibu -->
            <div>
                <h4 class="text-md font-semibold text-pink-400 mb-3"><i class="fas fa-female mr-2"></i>Data Ibu</h4>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Nama Ibu</label>
                        <input type="text" name="mother_name" value="{{ old('mother_name', $parentGuardian?->mother_name) }}" 
                            class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 text-white">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Pekerjaan</label>
                        <input type="text" name="mother_occupation" value="{{ old('mother_occupation', $parentGuardian?->mother_occupation) }}" 
                            class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 text-white">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Penghasilan / Bulan</label>
                        <input type="number" name="mother_income" value="{{ old('mother_income', $parentGuardian?->mother_income) }}" 
                            class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 text-white" placeholder="Rp">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">No. Telepon</label>
                        <input type="text" name="mother_phone_number" value="{{ old('mother_phone_number', $parentGuardian?->mother_phone_number) }}" 
                            class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 text-white">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-400 text-sm mb-1">Alamat</label>
                        <textarea name="mother_address" rows="2" 
                            class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 text-white">{{ old('mother_address', $parentGuardian?->mother_address) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Wali (Opsional) -->
            <div>
                <h4 class="text-md font-semibold text-yellow-400 mb-3"><i class="fas fa-user-shield mr-2"></i>Data Wali (Opsional)</h4>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Nama Wali</label>
                        <input type="text" name="guardian_name" value="{{ old('guardian_name', $parentGuardian?->guardian_name) }}" 
                            class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 text-white">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Pekerjaan</label>
                        <input type="text" name="guardian_occupation" value="{{ old('guardian_occupation', $parentGuardian?->guardian_occupation) }}" 
                            class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 text-white">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Penghasilan / Bulan</label>
                        <input type="number" name="guardian_income" value="{{ old('guardian_income', $parentGuardian?->guardian_income) }}" 
                            class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 text-white" placeholder="Rp">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">No. Telepon</label>
                        <input type="text" name="guardian_phone_number" value="{{ old('guardian_phone_number', $parentGuardian?->guardian_phone_number) }}" 
                            class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 text-white">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-400 text-sm mb-1">Alamat</label>
                        <textarea name="guardian_address" rows="2" 
                            class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 text-white">{{ old('guardian_address', $parentGuardian?->guardian_address) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Jumlah Tanggungan -->
            <div class="border-t border-slate-800 pt-4">
                <label class="block text-gray-400 text-sm mb-1">Jumlah Tanggungan Keluarga <span class="text-red-400">*</span></label>
                <input type="number" name="dependents_count" value="{{ old('dependents_count', $parentGuardian?->dependents_count ?? 0) }}" min="0"
                    class="w-full md:w-1/3 px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-white" required>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition">
                    <i class="fas fa-save mr-2"></i>Simpan Data Keluarga
                </button>
            </div>
        </form>
    </div>
</div>
@endsection