<?php

abstract class FestiPluginWithOptionsFilter extends FestiPlugin
{
    public function getOptions($optionName)
    {
        $options = parent::getOptions($optionName);

        $options = apply_filters(
            'festi_plugin_get_options',
            $options,
            $this->_optionsPrefix.$optionName
        );

        return $options;
    } // end getOptions

    public function updateOptions($optionName, $values = array())
    {    
        do_action(
            'festi_plugin_update_options',
            $values,
            $this->_optionsPrefix.$optionName
        );
           
        return parent::updateOptions($optionName, $values);
    } // end updateOptions
    
    public function getCache($optionName)
    {
    	if ($this->_isQtranslatePluginActive()) {
    		return false;
    	}
		
    	return parent::getCache($optionName);
    } // end getCache
    
    private function _isQtranslatePluginActive()
	{
		return $this->isPluginActive('qtranslate-x/qtranslate.php');
	} // end _isQtranslatePluginActive
}