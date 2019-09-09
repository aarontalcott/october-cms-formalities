<?php 

namespace Leapfrog\Formalities\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\CodeBase;
use October\Rain\Parse\Yaml;
use Leapfrog\Formalities\Classes\FieldsDataManipulator;
use Leapfrog\Formalities\Classes\FieldsFileFinder;

/**
 * @package leapfrog\formalities
 * @author Aaron Talcott
 */
class RenderForm extends ComponentBase
{

    /**
     * @var String
     */
    const MODELS_PATH = 'Models';

    /**
     * @var \Leapfrog\Formalities\Classes\FieldsFileFinder;
     */
    protected $fieldsFileFinder;

    /**
     * @var \Leapfrog\Formalities\Classes\FieldsDataManipulator
     */
    protected $fieldsDataManipulator;

    /**
     * @var \October\Rain\Parse\Yaml
     */
    protected $yaml;

    /**
     * @var \Cms\Classes\CodeBase $cmsObject
     * @var Array $properties
     * @var \Leapfrog\Formalities\Classes\FieldsDataManipulator $fieldsDataManipulator
     * @var \Leapfrog\Formaltties\Classes\FieldsFileFinder $fieldsFileFinder
     * @var \October\Rain\Parse\Yaml
     * @return void
     * @access public
     */
    public function __construct(
        CodeBase $cmsObject = null, 
        $properties = [],
        FieldsDataManipulator $fieldsDataManipulator, 
        FieldsFileFinder $fieldsFileFinder, 
        Yaml $yaml
    ) {
        $this->fieldsDataManipulator = $fieldsDataManipulator;
        $this->fieldsFileFinder = $fieldsFileFinder;
        $this->yaml = $yaml;
        parent::__construct($cmsObject, $properties);
    }

    /** 
     * Set the name and the description of the component
     * 
     * @return Array
     */
    public function componentDetails()
    {
        return [
            'name' => 'RenderForm',
            'description' => 'Automatically generate a frontend version of a backend model form'
        ];
    }

    /** 
     * Set page variables and add CSS and JS
     * 
     * @return void
     */
    public function onRun()
    {

        $this->page['fields'] = $this->getFieldsData();
        $this->page['plugin'] = $this->property('plugin');
        $this->page['model'] = $this->property('model');
        $this->page['showLabels'] = $this->property('showLabels', true);
        $this->page['validate'] = $this->property('useBrowserValidation', true);
        $this->page['includeCss'] = $this->property('includeCss', true);

        if ($this->property('includeCss', true)) {
            $this->addCss('assets/css/formalities.css');
            $this->addJs('assets/js/vanilla.datepicker.js');
            if (isset($this->page['fields']['tabs'])) {
                $this->addCss('assets/css/vanilla-js-tabs.css');
                $this->addJs('assets/js/vanilla-js-tabs.min.js');
            }
            $this->addJs('assets/js/formalities.js');
        }

    }

    /** 
     * Set the name and the description of the component
     * 
     * @return Array
     */
    public function getFieldsData() 
    {
        $fieldsFilePath = $this->fieldsFileFinder->find(
            $this->property('plugin'), 
            $this->property('model'), 
            $this->property('form')
        );
        try {
            $fieldsFileContents = @file_get_contents($fieldsFilePath);
        } catch (Exception $e) {
            throw new FormalitiesException("{$fieldsFilePath} could not be read. Check your values for the 'plugin', 'model', and 'form' properties.");
        }
        $fields = $this->yaml->parse($fieldsFileContents);
        $this->fieldsDataManipulator->injectNames($fields, $this->property('templatesDir'));
        $this->fieldsDataManipulator->restructureTabsData($fields);
        return $fields;
    }

    /** 
     * Endpoint for submission of forms generated using the component
     * 
     * Uses the "plugin" and "model" post vars to compile the name of the class
     * that should be created. Creates new instance, sets class attributes to 
     * POSTed values and attempts to save and returns the result
     * 
     * @return Array
     */
    public function onSave()
    {
        $modelClassElements = explode('.', post('plugin'));
        $modelClassElements = array_merge($modelClassElements, [self::MODELS_PATH, post('model')]);
        $className = implode("\\", $modelClassElements);
        $newModel = new $className;
        foreach (post(post('model')) as $key => $value) {
            if (!empty($value)) {
                $newModel->{$key} = $value;
            }
        }
        $result = $newModel->save();
        return ['result' => $result];
    }
}
