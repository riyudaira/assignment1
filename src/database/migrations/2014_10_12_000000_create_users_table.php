<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id'); // 主キー
            $table->string('name');
            $table->string('email')->unique(); // メールは重複不可
            $table->string('password');
            $table->string('post_code')->nullable(); //本番ではrequired
            $table->string('address')->nullable(); //本番ではrequired
            $table->string('build')->nullable(); // 建物名は任意
            $table->string('profile_image')->nullable(); // プロフィール画像（任意）
            $table->timestamp('email_verified_at')->nullable(); // 認証日時
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
