<?php

namespace Database\Seeders;

use App\Models\Filtro;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FiltroTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filtros = [
            [
                'nombre' => 'CISCO',
                'orden' => 1,
            ],
            [
                'nombre' => 'Microsoft',
                'orden' => 2,
            ],
            [
                'nombre' => 'Apache',
                'orden' => 3,
            ],
            [
                'nombre' => 'PHP',
                'orden' => 4,
            ],
            [
                'nombre' => 'MySQL',
                'orden' => 5,
            ],
            [
                'nombre' => 'Ubiquiti',
                'orden' => 6,
            ],
            [
                'nombre' => 'Hikvision',
                'orden' => 7,
            ],
            [
                'nombre' => 'Postgres',
                'orden' => 8,
            ],
            [
                'nombre' => 'Ubuntu',
                'orden' => 9,
            ],
            [
                'nombre' => 'Centos',
                'orden' => 10,
            ],
        ];
        Filtro::insert($filtros);
    }
}
