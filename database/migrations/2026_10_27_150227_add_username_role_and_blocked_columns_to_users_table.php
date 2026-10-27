<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique();
            $table->foreignId('role_id')->nullable()->constrained('roles')->restrictOnDelete();
            $table->boolean('is_blocked')->default(false);
        });

        $defaultRoleId = DB::table('roles')->where('name', 'user')->value('id');

        if ($defaultRoleId === null) {
            throw new RuntimeException('The default user role is missing.');
        }

        foreach (DB::table('users')->select('id')->orderBy('id')->get() as $user) {
            DB::table('users')->where('id', $user->id)->update([
                'username' => 'user_'.$user->id,
                'role_id' => $defaultRoleId,
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable(false)->change();
            $table->foreignId('role_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropUnique('users_username_unique');
            $table->dropColumn(['username', 'is_blocked']);
        });
    }
};
