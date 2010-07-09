<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Markus Martens <markus.martens@jobesoft.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Typoscript cObject' for the 'jqgrid' extension.
 *
 * @author	Markus Martens <markus.martens@jobesoft.de>
 * @package	TYPO3
 * @subpackage	tx_jqgrid
 */
class tx_jqgrid_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_jqgrid_pi1'; // Same as class name
	var $scriptRelPath = 'pi1/class.tx_jqgrid_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'jqgrid';	// The extension key.
  var $uid           = null;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
    $this->pi_initPIflexForm();
		$this->pi_USER_INT_obj = 0;
    
    // collect basic data
    $this->uid = intval($this->cObj->data['uid']);
    $piFlexForm = $this->cObj->data['pi_flexform'];
    if( $tskey = $this->pi_getFFvalue($piFlexForm,'table') )$this->conf['key'] = $tskey;
    
    // add css to page
    //$GLOBALS['TSFE']->additionalHeaderData['jqgrid-css-1'] = '<link rel="stylesheet" href="typo3conf/ext/jqgrid/res/css/redmond/jquery-ui-1.8.1.custom.css" type="text/css" />';
    $GLOBALS['TSFE']->additionalHeaderData['jqgrid-css-2'] = '<link rel="stylesheet" href="typo3conf/ext/jqgrid/res/css/ui.jqgrid.css" type="text/css" />';
    
    // add js to page
    if(t3lib_extMgm::isLoaded('t3jquery'))require_once(t3lib_extMgm::extPath('t3jquery').'class.tx_t3jquery.php');
    if(T3JQUERY === true){
      tx_t3jquery::addJqJS();
    }else{
      $GLOBALS['TSFE']->additionalHeaderData['jquery'] = '<script type="text/javascript" src="typo3conf/ext/jqgrid/res/js/jquery-1.4.2.min.js"></script>';
    }
    $GLOBALS['TSFE']->additionalHeaderData['jqgrid-js-1'] = '<script type="text/javascript" src="typo3conf/ext/jqgrid/res/js/i18n/grid.locale-de.js"></script>';//TODO: localize?
    $GLOBALS['TSFE']->additionalHeaderData['jqgrid-js-2'] = '<script type="text/javascript" src="typo3conf/ext/jqgrid/res/js/jquery.jqGrid.min.js"></script>';
    $GLOBALS['TSFE']->additionalHeaderData['jqgrid-js-'.$this->uid] = "<script type=\"text/javascript\">\n".$this->getJS()."</script>\n";
    
    // add html to page
    $content  = "<table id='jqgrid-".$this->uid."-list'></table>";
    $content .= "<div id='jqgrid-".$this->uid."-pager'></div>\n";
    
		return $this->pi_wrapInBaseClass($content);
	}
  
  // reply to ajax calls
	function ajax($content,$conf)	{

    $conf   = $conf[$_GET['tskey'].'.'];
    $page   = $_GET['page'];
    $limit  = $_GET['rows'];
    $sidx   = $_GET['sidx'];
    $sord   = $_GET['sord'];
    $table  = $conf['table'];
    $fields = array();
    $stdWrap = array();
    foreach($conf['columns.'] as $pos => $column){
      $fields[] = $column['name'];
      if($column['stdWrap.']) $stdWrap[$column['name']] = $column['stdWrap.'];// collect stdWraps
    }
    $fields = implode(',',$fields);
    
    if(!$sidx) $sidx = 1;
    
    $data = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('COUNT(*)',$table,null,null,null,null);
    $count = intval($data[0]['COUNT(*)']);
    
    $total_pages = ( $count > 0 && $limit > 0 )?ceil($count/$limit):0;
    if( $page > $total_pages ) $page = $total_pages;
    $start = $limit*$page - $limit;
    if( $start < 0 ) $start = 0;
     
    $where = null;
    $group = null;
    $order = sprintf('%s %s',$sidx,$sord);
    $limit = sprintf('%d,%d',$start,$limit);
    $data = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows($fields,$table,$where,$group,$order,$limit);
    
    $s  = "<?xml version='1.0' encoding='utf-8'?>\n";
    $s .= "<rows>\n";
    $s .= "  <page>".$page."</page>\n";
    $s .= "  <total>".$total_pages."</total>\n";
    $s .= "  <records>".$count."</records>\n";
    foreach( $data as $i => $row ){
      $s .= "  <row id='".$i."'>\n";
      foreach( $row as $key => $value){
        if($stdWrap[$key]) $value = $this->cObj->stdWrap($value,$stdWrap[$key]);// apply stdWrap if present
        $s .= "    <cell><![CDATA[".$value."]]></cell>\n";
      }
      $s .= "  </row>\n";
    }
    $s .= "</rows>\n";
    return $s;
  }
  
  // add js to page
  function getJS(){
  
    // prepare for jqgrid
    $conf = $this->conf[$this->conf['key'].'.'];
    $conf['url'] = 'index.php?id='.$this->cObj->data['pid'].'&type='.$conf['typeNum'].'&tskey='.$this->conf['key'];
    $conf['pager'] = '#jqgrid-'.$this->uid.'-pager';
    $conf['datatype'] = 'xml';
    $conf['mtype'] = 'GET';
    $conf['rowList'] = explode(',',$conf['rowList']);
    
    // columns
    foreach($conf['columns.'] as $pos => $field) $conf['colModel'][] = array_diff_key($field,array('stdWrap.'=>0));
    
    // tx_jqgrid_pi1 onlys
    unset($conf['table']);
    unset($conf['typeNum']);
    unset($conf['columns.']);
    
    // transform to js
    $jscode  = "jQuery(document).ready(function(){\n";
    $jscode .= "  jQuery(\"#jqgrid-".$this->uid."-list\").jqGrid(".self::array_js($conf,'  ').");\n";
    $jscode .= "});\n";
    
    return $jscode;
  }
  
  // converts an ts-array into an php-array
  private function array_ts($conf){
    return $conf;//TODO
  }
  
  // converts an php-array into an js-array
  private function array_js($conf,$prefix=''){
    $line = array();
    if(is_numeric(implode('',array_keys($conf)))){// numeric array
      foreach($conf as $key => $value){
        if(is_array($value))
          $line[] = sprintf( "%s", self::array_js($value,$prefix) );
        elseif(is_numeric($value))
          $line[] = sprintf( "%d", $value );
        elseif(!strcasecmp($value,'true'))
          $line[] = sprintf( "%s", 'true' );
        elseif(!strcasecmp($value,'false'))
          $line[] = sprintf( "%s", 'false' );
        else
          $line[] = sprintf( "'%s'", $value );
      }
      return "[".implode(",",$line)."]";
    }else{// assoziative array
      foreach($conf as $key => $value){
        if(is_array($value))
          $line[] = sprintf( $prefix."  %s:%s", rtrim($key,'.'), self::array_js($value,$prefix.'  ') );
        elseif(is_numeric($value))
          $line[] = sprintf( $prefix."  %s:%d", $key, $value );
        elseif(!strcasecmp($value,'true'))
          $line[] = sprintf( $prefix."  %s:true", $key );
        elseif(!strcasecmp($value,'false'))
          $line[] = sprintf( $prefix."  %s:false", $key );
        else
          $line[] = sprintf( $prefix."  %s:'%s'", $key, $value );
      }
      return "{\n".implode(",\n",$line)."\n".$prefix."}";
    }
  }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jqgrid/pi1/class.tx_jqgrid_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jqgrid/pi1/class.tx_jqgrid_pi1.php']);
}

?>