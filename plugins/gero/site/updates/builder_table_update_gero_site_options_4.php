<?php namespace Gero\Site\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateGeroSiteOptions4 extends Migration
{
    public function up()
    {
        Schema::table('gero_site_options', function($table)
        {
            $table->string('text')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('gero_site_options', function($table)
        {
            $table->dropColumn('text');
        });
    }
}
