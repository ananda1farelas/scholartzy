@extends('layouts.dashboard')

@section('title', 'Kelola Pengguna')

@section('content')
<div class="space-y-6" x-data="{ showModal: false, editMode: false, editUser: null }">
    <div class="flex items-center justify-between">
        @if ($errors->any())
            <div class="p-4 mb-4 bg-red-500/10 border border-red-500/50 rounded-lg">
                <ul class="list-disc list-inside text-sm text-red-400">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <h2 class="text-2xl font-bold">Kelola Pengguna</h2>
        <button @click="showModal = true; editMode = false; editUser = null;" 
                class="px-4 py-2.5 bg-red-600 hover:bg-red-700 rounded-lg text-sm font-medium transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Pengguna
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto -mx-4 sm:mx-0">
            <div class="min-w-[700px] sm:min-w-0 px-4 sm:px-0">
                <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-800">
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Nama</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Email</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Role</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Status</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-800/50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <span class="font-medium text-sm">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs font-medium 
                                {{ $user->user_role === 'admin' ? 'bg-red-500/10 text-red-400' : ($user->user_role === 'staff' ? 'bg-yellow-500/10 text-yellow-400' : 'bg-blue-500/10 text-blue-400') }}">
                                {{ ucfirst($user->user_role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs font-medium 
                                {{ $user->user_status === 'active' ? 'bg-green-500/10 text-green-400' : 'bg-gray-500/10 text-gray-400' }}">
                                {{ ucfirst($user->user_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button @click="showModal = true; editMode = true; editUser = {{ json_encode($user) }}" 
                                        class="p-2 text-blue-400 hover:bg-blue-500/10 rounded-lg transition">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if($user->user_id !== auth()->user()->user_id)
                                <form method="POST" action="{{ route('admin.users.delete', $user->user_id) }}" class="inline" onsubmit="return confirm('Yakin hapus pengguna ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-400 hover:bg-red-500/10 rounded-lg transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click="showModal = false" class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        <div class="relative bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-md p-6">
            <h3 class="text-xl font-bold mb-4" x-text="editMode ? 'Edit Pengguna' : 'Tambah Pengguna'"></h3>
            
            <form :action="editMode ? '/admin/users/' + editUser.user_id : '/admin/users'" method="POST" class="space-y-4">
                @csrf
                <template x-if="editMode">
                    @method('POST')
                </template>
                
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Nama</label>
                    <input type="text" name="name" :value="editMode ? editUser.name : ''" 
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-white" required>
                </div>
                
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Email</label>
                    <input type="email" name="email" :value="editMode ? editUser.email : ''" 
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-white" required>
                </div>
                
                <div x-show="!editMode">
                    <label class="block text-sm text-gray-400 mb-1">Password</label>
                    <input type="password" name="user_password" 
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-white" 
                        :required="!editMode">
                </div>
                
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Role</label>
                    <select name="user_role" :value="editMode ? editUser.user_role : 'student'" 
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-white">
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                        <option value="student">Student</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Status</label>
                    <select name="user_status" :value="editMode ? editUser.user_status : 'active'" 
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-white">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="showModal = false" class="px-4 py-2 border border-slate-700 rounded-lg text-gray-400 hover:text-white transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection