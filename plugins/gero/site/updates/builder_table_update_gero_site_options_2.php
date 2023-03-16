<?php namespace Gero\Site\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateGeroSiteOptions2 extends Migration
{
    public function up()
    {
        Schema::table('gero_site_options', function($table)
        {
            $table->integer('fedapay')->nullable()->change();
            $table->integer('paydunya')->nullable()->change();
            $table->integer('cinetpay')->nullable()->change();
            $table->integer('usdt')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('gero_site_options', function($table)
        {
            $table->integer('fedapay')->nullable(false)->change();
            $table->integer('paydunya')->nullable(false)->change();
            $table->integer('cinetpay')->nullable(false)->change();
            $table->integer('usdt')->nullable(false)->change();
        });
    }
}
