<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::disableForeignKeyConstraints();
Schema::dropIfExists('company_user');
Schema::dropIfExists('system_logs');
if (Schema::hasColumn('users', 'role_id')) {
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['role_id']);
        $table->dropColumn('role_id');
    });
}
Schema::dropIfExists('roles');
Schema::enableForeignKeyConstraints();
echo "Tables dropped.";
