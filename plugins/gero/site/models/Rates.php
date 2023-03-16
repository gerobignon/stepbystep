<?php namespace Gero\Site\Models;

use Model;

/**
 * Model
 */
class Rates extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'gero_site_rates';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
