<?php

########################################################################
# Extension Manager/Repository config file for ext: "dkd_redirect_at_login"
#
# Auto generated 24-10-2007 17:00
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Redirect user to page at login',
	'description' => 'Redirects the user to a specific page when he logs-in. The page to redirect to, can be specified using a database relation field of the fe_user record.',
	'category' => 'fe',
	'shy' => 1,
	'version' => '3.0.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => 'fe_groups',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Ingmar Schlecht',
	'author_email' => 'ingmars@web.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:9:{s:31:"class.ux_tx_newloginbox_pi1.php";s:4:"02e1";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"2e24";s:14:"ext_tables.php";s:4:"89bc";s:14:"ext_tables.sql";s:4:"7cf3";s:16:"locallang_db.php";s:4:"8183";s:14:"doc/manual.sxw";s:4:"8bca";s:19:"doc/wizard_form.dat";s:4:"75b3";s:20:"doc/wizard_form.html";s:4:"8ecc";}',
);

?>