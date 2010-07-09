<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

/*PI1*/
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';// hide ?,?,startingpoint
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';// you add pi_flexform to be renderd when your plugin is shown
t3lib_extMgm::addPlugin(array('LLL:EXT:'.$_EXTKEY.'/pi1/locallang.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
t3lib_extMgm::addStaticFile($_EXTKEY,'pi1/static/','jqGrid default setup');// add static typoscript
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/pi1/flexform.xml');// add flexform description file 
if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_jqgrid_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_jqgrid_pi1_wizicon.php';
include_once(t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_jqgrid_pi1_flexform.php');

?>