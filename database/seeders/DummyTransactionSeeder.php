<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummyTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = \App\Models\Product::all();
        $users = \App\Models\User::all();
        
        if ($products->isEmpty() || $users->isEmpty()) {
            $this->command->error("Requires at least 1 Product and 1 User to seed dummy transactions.");
            return;
        }

        $faker = \Faker\Factory::create('id_ID');
        $types = [\App\Models\StockTransaction::TIPE_IN, \App\Models\StockTransaction::TIPE_OUT];
        
        $jumlahTransaksi = 300; // Injecting 300 transactions

        for ($i = 0; $i < $jumlahTransaksi; $i++) {
            $product = $products->random();
            $user = $users->random();
            $tipe = $faker->randomElement($types);
            $qty = $faker->numberBetween(1, 50);
            
            // Random date within the last 90 days
            $tanggal = collect([
                now()->subDays(random_int(1, 90)),
                now()->subHours(random_int(1, 24)),
                now()->subMinutes(random_int(1, 60))
            ])->random();

            $tx = new \App\Models\StockTransaction();
            $tx->product_id = $product->id;
            $tx->user_id = $user->id;
            $tx->type = $tipe;
            $tx->quantity = $qty;
            
            if ($tipe === \App\Models\StockTransaction::TIPE_IN) {
                $tx->reference_id = 'IN-DUMMY-' . date('Ymd', $tanggal->timestamp) . str_pad($i, 4, '0', STR_PAD_LEFT);
                $tx->note = "Permintaan Dummy Supply " . current(explode(' ', $faker->company));
            } else {
                $tx->reference_id = 'OUT-DUMMY-' . date('Ymd', $tanggal->timestamp) . str_pad($i, 4, '0', STR_PAD_LEFT);
                $tx->note = "Kirim ke " . $faker->city . " a/n " . $faker->name;
            }

            // Force timestamps to bypass Laravel's auto-now() insertion
            $tx->created_at = $tanggal;
            $tx->updated_at = $tanggal;
            $tx->saveQuietly(); // Use saveQuietly to potentially avoid triggering Observers if they exist
        }
        
        $this->command->info("Seeded {$jumlahTransaksi} dummy transactions across the last 90 days.");
    }
}
