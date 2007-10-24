<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
$tempColumns = Array (
	"tx_dkdredirectatlogin_redirectpage" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:dkd_redirect_at_login/locallang_db.php:fe_groups.tx_dkdredirectatlogin_redirectpage",		
		"config" => Array (
			"type" => "group",	
			"internal_type" => "db",	
			"allowed" => "pages",	
			"size" => 1,	
			"minitems" => 0,
			"maxitems" => 1,
		)
	),
);


t3lib_div::loadTCA("fe_groups");
t3lib_extMgm::addTCAcolumns("fe_groups",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("fe_groups","tx_dkdredirectatlogin_redirectpage;;;;1-1-1");
?>