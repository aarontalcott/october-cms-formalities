<?php namespace Leapfrog\Formalities;

use Backend;
use System\Classes\PluginBase;

/**
 * Formalities Plugin Information File
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
            'name'        => 'Formalities',
            'description' => 'Automatically render back-end forms on the front end.',
            'author'      => 'Aaron Talcott',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Leapfrog\Formalities\Components\RenderForm' => 'renderForm',
        ];
    }

}
