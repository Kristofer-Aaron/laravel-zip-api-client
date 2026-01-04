<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\County;

class CountiesSeeder extends Seeder
{
    public function run(): void
    {
        $path = storage_path('app/megyek.csv');
        if (!file_exists($path)) {
            $this->command->error("CSV fájl nem található: $path");
            return;
        }

        $handle = fopen($path, 'r');
        $count = 0;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $name = trim($row[0], "\xEF\xBB\xBF \t\n\r\0\x0B");

            if (!$name) continue; // skip empty lines

            County::firstOrCreate(['name' => $name]);

            $count++;
        }

        fclose($handle);
        $this->command->info("Import sikeres: {$count} megye betöltve.");
    }
}
