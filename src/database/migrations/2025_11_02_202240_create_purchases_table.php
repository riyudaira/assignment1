<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->bigIncrements('id'); // 主キー
            $table->foreignId('item_id')->constrained()->onDelete('cascade'); // 購入された商品
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 購入者
            $table->enum('payment_method', ['コンビニ払い', 'カード支払い',]); // 支払い方法（仕様に合わせて調整）
            $table->string('post_code');
            $table->string('address');
            $table->string('build')->nullable(); // 建物名は任意
            $table->timestamp('purchased_at'); // 購入日時
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}
