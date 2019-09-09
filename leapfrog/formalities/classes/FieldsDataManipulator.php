<?php namespace Leapfrog\Formalities\Classes;

/**
 * Modifies an array of parsed YAML form data for consumption
 * by front-end templates
 *
 * @package leapfrog\formalities
 * @author Aaron Talcott
 */
class FieldsDataManipulator
{

    /**
     * Check for presence of non-tabbed fields and tabbed fields
     * and call injectFieldNames on each where necessary
     *
     * @param Array $fields
     * @return void
     */
    public function injectNames(Array &$fields)
    {
        if (isset($fields['fields'])) {
            $this->injectFieldNames($fields['fields']);
        }
        if (isset($fields['tabs']) && isset($fields['tabs']['fields'])) {
            $this->injectFieldNames($fields['tabs']['fields']);
        }
    }

    /**
     * Injects a "name" property into each field's array with the field's
     * key as the value
     *
     * @param Array $fields
     * @return void
     */
    protected function injectFieldNames(Array &$fields)
    {
        foreach ($fields as $key => &$field) {
            $field['name'] = $key;
        }
    }

    /**
     * Move fields inside the 'tabs' array as a series
     * arrays, each corresponding to a tab, based on the fields'
     * "tab" property
     *
     * @return void
     */
    public function restructureTabsData(Array &$fields) 
    {
        if (empty($fields['tabs'])) {
            return;
        }
        $tabs = [];
        foreach ($fields['tabs']['fields'] as $name => $tabField) {
            if (!isset($tabs[$tabField['tab']])) {
                $tabs[$tabField['tab']] = [];
            }
            $tabs[$tabField['tab']][$name] = $tabField;
        }
        $fields['tabs'] = $tabs;
    }


}