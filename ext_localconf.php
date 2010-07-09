<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

/* register plugins */
t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_jqgrid_pi1.php','_pi1','list_type',0);

?>