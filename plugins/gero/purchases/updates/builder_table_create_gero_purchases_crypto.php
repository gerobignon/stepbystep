<?php namespace Gero\Purchases\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateGeroPurchasesCrypto extends Migration
{
    public function up()
    {
        Schema::create('gero_purchases_crypto', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('user_id');
            $table->double('amount', 10, 0);
            $table->string('ref');
            $table->text('address')->nullable();
            $table->string('currency');
            $table->string('status');
            $table->string('hash')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('gero_purchases_crypto');
    }
}
