<?php namespace Leapfrog\Formalities\Classes;

use Leapfrog\Formalities\Classes\FormalitiesException;

/**
 * Locates the form's YAML definition file based 'plugin', 'model', and 'form'
 * component properties and returns its contents
 *
 * @package leapfrog\formalities
 * @author Aaron Talcott
 */
class FieldsFileFinder
{

    /**
     * Used if no 'form' component property is specified
     *
     * @var String
     */
    protected const FIELDS_FILE_NAME = 'fields';

    /**
     * Name of directory in plugin directory where model
     * classes are located. In October this is always
     * 'models'
     *
     * @var String
     */
    protected const MODELS_DIRECTORY_NAME = 'models';

    /**
     * @var String
     */
    protected const FIELDS_FILE_EXTENSION = 'yaml';

    /**
     * Get the fields file contents 
     * 
     * @param String $plugin the name of the plugin e.g. Vendor.Plugin
     * @param String $model the name of the model
     * @param String $form the name of the form definition file excluding the extension
     * @return String the path to the form definition file
     * @throws FormalitiesException
     * @access public
     */
    public function find(String $plugin, String $model, String $form = self::FIELDS_FILE_NAME)
    {
        $pluginDirectory = $this->getPluginDirectory($plugin);
        $modelDirectory = $this->getModelDirectory($model);
        $fieldsFileDirectory = $this->getFieldsFileDirectory($pluginDirectory, $modelDirectory);
        $fieldsFileName = $this->getFieldsFileName($form);
        return $fieldsFileDirectory.$fieldsFileName.'.'.self::FIELDS_FILE_EXTENSION;
    }
    
    /**
     * Validate the supplied plugin name and return the directory 
     * derived from it
     *
     * @param String $plugin
     * 
     * @return String the directory of the plugin
     * @throws FormalitiesException
     * @access protected
     */
    protected function getPluginDirectory(String $plugin)
    {
        $pluginNameComponents = explode('.', $plugin);
        if (count($pluginNameComponents) !== 2) {
            throw new FormalitiesException("The value of the 'plugin' property must be in the format 'Vendor.Plugin'. The value you provided was '{$pluginName}'.");
        }
        $pluginNameComponents = array_map(function ($n) {
            return strtolower($n);
        }, $pluginNameComponents);
        return implode('/', $pluginNameComponents);
    }

    /**
     * Get the name of the model directory from the supplied 'model' component
     * property
     *
     * @param String $modelName
     * @return String the model directory name
     * @access protected
     */
    protected function getModelDirectory(String $modelName)
    {
        return strtolower($modelName);
    }

    /**
     * Get the full directory path of the form definition file 
     *
     * @param String $pluginDirectory
     * @param String $modelDirectory
     * @return String
     * @access protected
     */
    protected function getFieldsFileDirectory(String $pluginDirectory, String $modelDirectory)
    {
        return plugins_path($pluginDirectory."/".self::MODELS_DIRECTORY_NAME."/".$modelDirectory."/");
    }

    /**
     * Return the supplied "form" component property or the default file name
     *
     * @param String $fieldsFileName
     * @return String
     * @access protected
     */
    protected function getFieldsFileName(String $fieldsFileName = null)
    {
        if ($fieldsFileName) {
            return $fieldsFileName;
        }
        return self::FIELDS_FILE_NAME;
    }

}