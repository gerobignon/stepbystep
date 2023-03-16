<?php namespace Gero\Site\Models;

use Model;

/**
 * Model
 */
class Logs extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'gero_site_logs';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
