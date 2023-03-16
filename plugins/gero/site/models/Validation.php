<?php namespace Gero\Site\Models;

use Model;

/**
 * Model
 */
class Validation extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'gero_site_validation';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $belongsTo = [
        'user' => 'RainLab\User\Models\User'
    ];

    public $attachOne = [
        "idcard" => "System\Models\File",
        "selfie" => "System\Models\File"
    ];
    
}
