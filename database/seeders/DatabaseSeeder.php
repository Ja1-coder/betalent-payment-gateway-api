<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Gateway;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Criar Usuários para testes de Roles (Nível 2/3)
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@teste.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        User::create([
            'name' => 'Finance User',
            'email' => 'finance@teste.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_FINANCE,
        ]);

        // 2. Criar os Gateways (Aqui definimos a prioridade do teste)
        Gateway::create([
            'name' => 'Gateway 1',
            'is_active' => true,
            'priority' => 1, // Este será tentado primeiro
        ]);

        Gateway::create([
            'name' => 'Gateway 2',
            'is_active' => true,
            'priority' => 2, // Se o 1 falhar, o sistema tenta este
        ]);

        // 3. Criar Produtos para teste de cálculo no Back-end
        Product::create([
            'name' => 'Smartphone High-End',
            'amount' => 500000, // R$ 5.000,00
        ]);

        Product::create([
            'name' => 'Fone de Ouvido Bluetooth',
            'amount' => 25000, // R$ 250,00
        ]);
        
        $this->command->info('Banco de dados povoado com sucesso!');
    }
}
