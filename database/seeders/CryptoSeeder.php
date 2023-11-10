<?php

namespace Database\Seeders;

use App\Models\Crypto;
use App\Models\GithubProject;
use Illuminate\Database\Seeder;

class CryptoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         Crypto::factory(1)->state([
             'name' => 'solana',
             'token' => 'sol'
         ])->has(GithubProject::factory()->state([
             'owner_name' => 'solana-labs',
             'repository_name' => 'solana',
         ]))->create();
    }
}
