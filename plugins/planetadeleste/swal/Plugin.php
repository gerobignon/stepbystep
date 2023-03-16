<?php namespace PlanetaDelEste\Swal;

use Backend;
use System\Classes\PluginBase;

/**
 * Swal Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'planetadeleste.swal::lang.plugin.name',
            'description' => 'planetadeleste.swal::lang.plugin.description',
            'author'      => 'PlanetaDelEste',
            'icon'        => 'icon-bell'
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'planetadeleste.swal::lang.plugin.name',
                'description' => 'planetadeleste.swal::lang.plugin.description',
                'icon'        => 'icon-bell',
                'url'         => Backend::url('planetadeleste/swal/settings'),
                'order'       => 501,
                'category'    => 'system::lang.system.categories.cms',
                'keywords'    => 'flash message sweet alert swal',
                'homepage'    => 'https://bitbucket.org/planetadeleste/oc-sweetalert',
                'permissions' => ['planetadeleste.swal.access_settings'],
            ],
        ];
    }

    public function registerPermissions()
    {
        return [
            'planetadeleste.swal.access_settings' => [
                'label' => 'planetadeleste.swal::lang.permissions.access_settings',
                'tab'   => 'planetadeleste.swal::lang.plugin.name'
            ]
        ];
    }

    public function registerComponents()
    {
        return [
            'PlanetaDelEste\Swal\Components\Swal' => 'sweetAlertMessage',
        ];
    }

}
