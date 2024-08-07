<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::create([
            'name' => "teste",
            'email' => "teste@teste.com",
            'password' => Hash::make("12345678"),
        ]);

        $categories = [
            'Outros', 'Educação', 'Lazer', 'Saúde', 'Viagem', 'Supermercado', 'Eletrônicos',
            'Casa', 'Serviços', 'Transporte', 'Vestuário', 'Restaurante'
        ];

        foreach ($categories as $category) {
            Category::create(['name' => $category, 'user_id' => $user->id]);
        }
    }
}
