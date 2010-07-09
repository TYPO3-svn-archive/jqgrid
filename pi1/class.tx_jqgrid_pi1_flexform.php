<?php
  
  class tx_jqgrid_pi1_flexform {
    
    function addFields ($config) {
      $optionList = array();
      // add first option
      $optionList[0] = array(0 => 'option1', 1 => 'value1');
      // add second option
      $optionList[1] = array(0 => 'option2', 1 => 'value2');
      $config['items'] = array_merge($config['items'],$optionList);
      return $config;
    }
    
  }

?>