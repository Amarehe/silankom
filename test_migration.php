<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Cek struktur tabel users
    $columns = DB::select('DESCRIBE users');
    echo "Struktur tabel users:\n";
    foreach ($columns as $column) {
        echo "  - {$column->Field} ({$column->Type})\n";
    }

    echo "\n\nSample users:\n";
    $users = DB::table('users')->limit(5)->get();
    foreach ($users as $user) {
        print_r($user);
        echo "\n";
    }

} catch (\Exception $e) {
    echo '❌ ERROR: '.$e->getMessage()."\n";
}
