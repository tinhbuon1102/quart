<?php

abstract class FestiPluginChild extends FestiPlugin
{    
    public function getOptions($optionName)
    {
        $options = $this->getCache($optionName);
        
        if (!$options){
           $options = get_option($this->_optionsPrefix.$optionName); 
        }
        
        if ($this->isJson($options)) {
            $options = json_decode($options, true);
        } else {
            $options = unserialize($options);
        }
           
        return $options;
    } // end getOptions
    
    public function updateOptions($optionName, $values = array())
    {
        $values = $this->doChangeSingleQuotesToSymbol($values);
        
        $value = serialize($values);

        update_option($this->_optionsPrefix.$optionName, $value);
        
        $result = $this->updateCacheFile($optionName, $value);

        return $result;
    } // end updateOptions
    
    protected function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    
    protected function doChangeSingleQuotesToSymbol($options = array())
    {
        foreach ($options as $key => $value) {
            if (!is_string($value)) {
                continue;
            } 
            
            $result = str_replace("'", '&#039;', $value);
            $options[$key] = stripslashes($result);
        }
        
        return $options;
    } // end doChangeSingleQuotesToSymbol
}