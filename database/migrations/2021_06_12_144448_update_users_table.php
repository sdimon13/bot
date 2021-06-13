<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->softDeletes();
        });

        \App\Models\User::each(function ($item) {
           $item->email = $item->name . '@bot.api';
           $item->password = bcrypt(123456);
           $item->save();
        });

        \DB::statement("ALTER TABLE users ALTER COLUMN email SET NOT NULL");
        \DB::statement("ALTER TABLE users ADD CONSTRAINT email_unique UNIQUE (email);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('email_verified_at');
            $table->dropColumn('password');
            $table->dropColumn('remember_token');
            $table->dropColumn('deleted_at');
        });
    }
}
