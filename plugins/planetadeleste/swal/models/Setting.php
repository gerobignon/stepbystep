<?php namespace PlanetaDelEste\Swal\Models;

use File;
use Model;

/**
 * Setting Model
 * @property mixed|string title
 * @property mixed|string global_title
 * @property mixed|object options
 * @property null|boolean is_global
 */
class Setting extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'planetadeleste_swal_settings';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    protected $jsonable = ['options'];

    public $rules = ['title' => 'required'];

    public function getInputTypeOptions()
    {
        return [
            'text'           => 'text',
            'password'       => 'password',
            'color'          => 'color',
            'date'           => 'date',
            'datetime'       => 'datetime',
            'datetime-local' => 'datetime-local',
            'email'          => 'email',
            'month'          => 'month',
            'number'         => 'number',
            'range'          => 'range',
            'search'         => 'search',
            'tel'            => 'tel',
            'time'           => 'time',
            'url'            => 'url',
            'week'           => 'week',
        ];
    }

    public function getGlobalTitleOptions()
    {
        return [
            'ajaxErrorMessage'   => 'ajaxErrorMessage',
            'ajaxError'          => 'ajaxError',
            'ajaxConfirmMessage' => 'ajaxConfirmMessage',
            'ajaxFlashMessages'  => 'ajaxFlashMessages',
        ];
    }

    public function getThemeOptions()
    {
        $themes = [
            'default' => trans('planetadeleste.swal::lang.settings.default_theme'),
        ];
        $dirs = File::directories(plugins_path('planetadeleste/swal/assets/vendor/sweetalert/themes'));
        if (is_array($dirs)) {
            foreach ($dirs as $dir) {
                $name = basename($dir);
                if (File::exists($dir.'/'.$name.'.css')) {
                    $themes[$name] = ucfirst($name);
                }
            }
        }

        return $themes;
    }

    public function getAnimationOptions()
    {
        return [
            'pop'               => trans('planetadeleste.swal::lang.settings.animation_pop'),
            'slide-from-top'    => trans('planetadeleste.swal::lang.settings.animation_slide_from_top'),
            'slide-from-bottom' => trans('planetadeleste.swal::lang.settings.animation_slide_from_bottom')
        ];
    }

    public function scopeIsCustom($query)
    {
        return $query->where(
            function ($query) {
                $query->whereNull('is_global')->orWhere('is_global', '0');
            }
        );
    }

    public function scopeIsGlobal($query)
    {
        return $query->where('is_global', '1');
    }

    public function getGlobalTitleAttribute()
    {
        return $this->title;
    }

    public function setGlobalTitleAttribute($value)
    {
        $this->attributes['global_title'] = ($this->is_global) ? $this->title : $value;
    }

    public function getThemeAttribute()
    {
        return array_get($this->options, 'theme', 'default');
    }

    public function beforeValidate()
    {
        if ($this->is_global) {
            $this->title = post('Setting.global_title');
        }

        unset($this->global_title);
    }


}