<?php

use Illuminate\Database\Seeder;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('departments')->truncate();
        Schema::enableForeignKeyConstraints();

        $departments = [
            ['name' => 'Digital Marketing', 'description' => 'Fokus pada strategi pemasaran digital, optimasi SEO, iklan berbayar (SEM), manajemen media sosial, dan kampanye email untuk meningkatkan visibilitas merek dan konversi secara online.'],
            ['name' => 'Design', 'description' => 'Departemen Desain bertanggung jawab menciptakan identitas visual yang kuat, antarmuka pengguna (UI) yang intuitif, serta elemen grafis kreatif untuk berbagai platform digital maupun media cetak.'],
            ['name' => 'Markom', 'description' => 'Mengintegrasikan komunikasi pemasaran untuk membangun pesan yang konsisten di semua saluran, mengelola hubungan masyarakat, serta merencanakan acara dan promosi merek.'],
            ['name' => 'Web Dev', 'description' => 'Terdiri dari tim pengembang yang merancang, membangun, dan memelihara infrastruktur situs web serta aplikasi berbasis web dengan standar teknologi terkini untuk pengalaman pengguna yang optimal.'],
        ];

        foreach ($departments as $dept) {
            Department::create([
                'name' => $dept['name'],
                'slug' => Str::slug($dept['name']),
                'description' => $dept['description'],
            ]);
        }
    }
}
