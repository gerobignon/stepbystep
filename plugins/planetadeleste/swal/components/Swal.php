<?php namespace PlanetaDelEste\Swal\Components;

use Cms\Classes\ComponentBase;
use Flash;
use PlanetaDelEste\Swal\Models\Setting as SwalSetting;

class Swal extends ComponentBase
{

    /**
     * @var array
     */
    public $options;
    /**
     * @var boolean
     */
    public $flag;

    /**
     * @var string
     */
    public $theme;

    public function componentDetails()
    {
        return [
            'name'        => 'planetadeleste.swal::lang.plugin.name',
            'description' => 'planetadeleste.swal::lang.plugin.description',
        ];
    }

    public function defineProperties()
    {
        return [
            'configuration' => [
                'title'             => 'planetadeleste.swal::lang.settings.setting',
                'type'              => 'dropdown',
                'default'           => '',
                'showExternalParam' => false,
            ],
        ];
    }

    public function getConfigurationOptions()
    {
        $arr = SwalSetting::isCustom()->lists('title', 'id');
        $arr[''] = '...';

        return $arr;
    }

    public function onRun()
    {
        $this->getParam();

        $theme = $this->theme;
        $this->addCss('/plugins/planetadeleste/swal/assets/vendor/sweetalert/sweetalert.css');
        if($theme != 'default') {
            $this->addCss("/plugins/planetadeleste/swal/assets/vendor/sweetalert/themes/{$theme}/{$theme}.css");
        }
        $this->addJs('/plugins/planetadeleste/swal/assets/vendor/sweetalert/sweetalert.min.js');
        $this->addJs('/plugins/planetadeleste/swal/assets/js/flash-swal.js');
    }

    public function onShowFlashMsg()
    {
        $msg = Flash::all();
        if ($msg) {
            $res['msgs'] = $msg;
            $res['options'] = [];
            if ($this->property('configuration')) {
                $res['options'] = $this->setParams(SwalSetting::find($this->property('configuration'))->toArray());
            }

            return $res;
        }
    }

    private function getParam()
    {
        $options = ($this->property('configuration')) ? SwalSetting::find($this->property('configuration'))->options : [];
        $this->theme = array_get($options, 'theme', 'default');
        if(array_get($options, 'use_default_options')) {
            $options = [];
        }
        $this->options = $this->page['options'] = $this->setParams($options);

        $global_options = [];
        $global = SwalSetting::isGlobal()->get();
        if ($global) {
            $global->each(
                function ($item) use (&$global_options) {
                    $global_options[$item->title] = $item->options;
                }
            );
        }
        $this->page['globalOptions'] = $this->setParams($global_options);
    }

    private function setParams($options)
    {
        $bools = [
            'allowEscapeKey',
            'allowOutsideClick',
            'showCancelButton',
            'showConfirmButton',
            'closeOnConfirm',
            'closeOnCancel',
            'html',
            'animation',
            'showLoaderOnConfirm'
        ];

        $ints = [
            'timer'
        ];

        if(is_array($options)) {
            foreach($options as $k => $v) {
                if(is_array($v)) {
                    $options[$k] = $this->setParams($v);
                } else {
                    if(in_array($k, $bools)) {
                        $options[$k] = (bool)$v;
                    } else if(in_array($k, $ints)) {
                        $options[$k] = (int)$v;
                    }
                }
            }
        }

        return $options;
    }

}