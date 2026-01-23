<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        DB::table('department_members')->truncate();
        Schema::enableForeignKeyConstraints();

        $users = [
            ['id' => 1, 'name' => 'Admin User', 'email' => 'admin@example.com', 'role' => 'admin', 'monthly_energy_limit' => 176],
            ['id' => 2, 'name' => 'Client User', 'email' => 'client@example.com', 'role' => 'client', 'monthly_energy_limit' => 176],
            ['id' => 3, 'name' => 'Regular User', 'email' => 'user@example.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 4, 'name' => 'Suryo Atmojo', 'email' => 'suryo@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 5, 'name' => 'Yogi Pradhokot', 'email' => 'yogi@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 6, 'name' => 'Alfin Fachrizal', 'email' => 'alfin@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 7, 'name' => 'Danu Nur', 'email' => 'danu@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 8, 'name' => 'Andrik Suprayogi', 'email' => 'andrik@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 9, 'name' => 'Dev User', 'email' => 'dev@example.com', 'role' => 'admin', 'monthly_energy_limit' => 176],
            ['id' => 10, 'name' => 'Guest User', 'email' => 'guest@system.local', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 11, 'name' => 'Endho Yuliansyah', 'email' => 'endho@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 12, 'name' => 'Moh. Alfin Kholily', 'email' => 'malfin@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 13, 'name' => 'AHMAD BAHARUDDIN YUSUF', 'email' => 'aby@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 14, 'name' => 'BAGUS AJI PUTRA PRASTOWO', 'email' => 'bagus@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 15, 'name' => 'Yanuar Laudy', 'email' => 'yanuar@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 16, 'name' => 'MUHAMMAD MARUF', 'email' => 'maruf@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 17, 'name' => 'Tia Nabilla', 'email' => 'tia@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 18, 'name' => 'ALICIA SYAFA KIRANA', 'email' => 'alice@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 19, 'name' => 'CLARISSA JANICE NOERJANTO', 'email' => 'jenice@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 20, 'name' => 'INTADHIRIS SHIFIA', 'email' => 'inta@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 21, 'name' => 'LUVITA KARTIKA SARI', 'email' => 'vita@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 22, 'name' => 'SITI NURUL MAISURA', 'email' => 'tinuk@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 23, 'name' => 'SITI ATIKASARI', 'email' => 'tika@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 24, 'name' => 'NUNIK INDAH PRATIWI', 'email' => 'indah@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 25, 'name' => 'AHMAD YUSLIH EL FAHMI', 'email' => 'yuslih@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 26, 'name' => 'Agung Murfatiaji', 'email' => 'agung@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 27, 'name' => 'Christopher Imandela', 'email' => 'christopher@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
            ['id' => 28, 'name' => 'ROZI AHMAD SUBKHI AZAL', 'email' => 'rozi@indraco.com', 'role' => 'user', 'monthly_energy_limit' => 176],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'role' => $userData['role'],
                'password' => Hash::make('password'),
                'monthly_energy_limit' => $userData['monthly_energy_limit'],
            ]);

            // Assign specific users to Web Dev department (id 4 in seeder)
            // Based on original data: Suryo(4), Yogi(5), Alfin(6), Andrik(8), Danu(7)
            // In new seeder order: Suryo(4), Yogi(5), Alfin(6), Danu(7), Andrik(8)
            $webDevUserIds = [4, 5, 6, 7, 8];
            if (in_array($user->id, $webDevUserIds)) {
                $role = ($user->id == 4) ? 'SPV' : 'Staff';
                $user->departments()->attach(4, [
                    'role' => $role,
                    'joined_at' => now(),
                ]);
            }
        }
    }
}
