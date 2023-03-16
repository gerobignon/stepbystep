<?php namespace Gero\Site\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateGeroSiteOptions extends Migration
{
    public function up()
    {
        Schema::table('gero_site_options', function($table)
        {
            $table->integer('fedapay');
            $table->integer('paydunya');
            $table->integer('cinetpay');
            $table->integer('usdt');
        });
    }
    
    public function down()
    {
        Schema::table('gero_site_options', function($table)
        {
            $table->dropColumn('fedapay');
            $table->dropColumn('paydunya');
            $table->dropColumn('cinetpay');
            $table->dropColumn('usdt');
        });
    }
}
