<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@scholartzy.com',
            'user_password' => Hash::make('admin123'),
            'user_role' => 'admin',
            'user_status' => 'active',
        ]);

        // Staff Kemahasiswaan
        User::create([
            'name' => 'Staff Kemahasiswaan',
            'email' => 'staff@scholartzy.com',
            'user_password' => Hash::make('staff123'),
            'user_role' => 'staff',
            'user_status' => 'active',
        ]);

        // Mahasiswa 1
        $student1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@student.com',
            'user_password' => Hash::make('123456789'),
            'user_role' => 'student',
            'user_status' => 'active',
        ]);

        // Mahasiswa 2
        $student2 = User::create([
            'name' => 'Ani Wulandari',
            'email' => 'ani@student.com',
            'user_password' => Hash::make('123456789'),
            'user_role' => 'student',
            'user_status' => 'active',
        ]);

        // Mahasiswa 3
        User::create([
            'name' => 'Cahyo Nugroho',
            'email' => 'cahyo@student.com',
            'user_password' => Hash::make('123456789'),
            'user_role' => 'student',
            'user_status' => 'active',
        ]);

        // Opsional: Bikin data student profil juga kalo mau
        \App\Models\Student::create([
            'user_id' => $student1->user_id,
            'student_number' => '20210001',
            'full_name' => 'Budi Santoso',
            'birth_date' => '2002-05-15',
            'gender' => 'male',
            'phone_number' => '081234567890',
            'address' => 'Jl. Mawar No. 1, Jakarta',
            'study_program' => 'Teknik Informatika',
            'semester' => 5,
        ]);

        \App\Models\Student::create([
            'user_id' => $student2->user_id,
            'student_number' => '20210002',
            'full_name' => 'Ani Wulandari',
            'birth_date' => '2003-01-20',
            'gender' => 'female',
            'phone_number' => '082345678901',
            'address' => 'Jl. Melati No. 2, Bandung',
            'study_program' => 'Sistem Informasi',
            'semester' => 3,
        ]);
    }
}