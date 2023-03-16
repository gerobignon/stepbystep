<?php namespace Gero\Site\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateGeroSiteOptions5 extends Migration
{
    public function up()
    {
        Schema::table('gero_site_options', function($table)
        {
            $table->integer('kkiapay');
        });
    }
    
    public function down()
    {
        Schema::table('gero_site_options', function($table)
        {
            $table->dropColumn('kkiapay');
        });
    }
}
