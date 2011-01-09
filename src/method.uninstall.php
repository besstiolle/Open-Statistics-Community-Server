<?php


if (!isset($gCms)) exit;


$db =& $gCms->GetDb();
$dict = NewDataDictionary( $db );

// remove the database module_oscs_user
$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_oscs_user" );
$dict->ExecuteSQLArray($sqlarray);

// remove the database module_oscs_rapport
$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_oscs_rapport" );
$dict->ExecuteSQLArray($sqlarray);

// remove the sequence
$db->DropSequence( cms_db_prefix()."module_oscs_user_seq" );
$db->DropSequence( cms_db_prefix()."module_oscs_rapport_seq" );

// remove the permissions
$this->RemovePermission('Set Open Statistics Community Server Prefs');

$this->DeleteTemplate();
$this->RemovePreference();

// put mention into the admin log
$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('uninstalled'));

?>