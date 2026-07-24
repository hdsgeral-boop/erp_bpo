<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (\App\Models\Company::all() as $c) {
    if (!$c->trial_ends_at) {
        $c->update([
            'trial_ends_at' => now()->addDays(30),
            'subscription_status' => 'trial'
        ]);
        echo "Updated company {$c->id}: {$c->name} with 30-day trial.\n";
    }
}
