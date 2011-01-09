<?php


if (!isset($gCms)) exit;


$db =& $gCms->GetDb();

$taboptarray = array( 'mysql' => 'TYPE=MyISAM' );

$dict = NewDataDictionary( $db );


/** TABLE CONTENANT LES USERS **/
$flds = "
     id I KEY,
	 cni C(50) ,
	 clepublic C(50) ,
	 cleprivee C(50) ,
	 date_creation " . CMS_ADODB_DT . " ,
	 date_update " . CMS_ADODB_DT . "
";
			
$sqlarray = $dict->CreateTableSQL( cms_db_prefix()."module_oscs_user",
				   $flds, 
				   $taboptarray);
$dict->ExecuteSQLArray($sqlarray);


$query = 'ALTER TABLE '.cms_db_prefix().'module_oscs_user ADD INDEX (date_creation)';
if ($db->Execute($query) === false)
{
	print_r($db);
	die('erreur grave durant l\'installation 1 ');
}
$query = 'ALTER TABLE '.cms_db_prefix().'module_oscs_user ADD INDEX (id)';
if ($db->Execute($query) === false)
{
	die('erreur grave durant l\'installation 2');
}

$query = 'ALTER TABLE '.cms_db_prefix().'module_oscs_user ADD INDEX (cni)';
if ($db->Execute($query) === false)
{
	die('erreur grave durant l\'installation 3');
}

$db->CreateSequence(cms_db_prefix()."module_oscs_user_seq");


/** TABLE CONTENANT LES RAPPORTS **/
$flds = "
     id I KEY,
	 user_id I ,
	 reponse C(10),
	 rapport X,
	 date_reception " . CMS_ADODB_DT . "
";
			
//TODO : verifier les erreurs
$sqlarray = $dict->CreateTableSQL( cms_db_prefix()."module_oscs_rapport",
				   $flds, 
				   $taboptarray);
$dict->ExecuteSQLArray($sqlarray);


$query = 'ALTER TABLE '.cms_db_prefix().'module_oscs_rapport ADD INDEX (date_reception)';
if ($db->Execute($query) === false)
{
	die('erreur grave durant l\'installation 6');
}
$query = 'ALTER TABLE '.cms_db_prefix().'module_oscs_rapport ADD INDEX (user_id)';
if ($db->Execute($query) === false)
{
	die('erreur grave durant l\'installation 7');
}

$query = 'ALTER TABLE '.cms_db_prefix().'module_oscs_rapport ADD INDEX (reponse)';
if ($db->Execute($query) === false)
{
	die('erreur grave durant l\'installation 8');
}
// create a sequence
$db->CreateSequence(cms_db_prefix()."module_oscs_rapport_seq");

/** TABLE CONTENANT LES PARTIES DE RAPPORTS MULTIPLES **/
$flds = "
     id I KEY,
	 cni C(50) ,
	 resume C(32),
	 size I,
	 nbpart I,
	 data X
";
			
//TODO : verifier les erreurs
$sqlarray = $dict->CreateTableSQL( cms_db_prefix()."module_oscs_rapport_tmp",
				   $flds, 
				   $taboptarray);
$dict->ExecuteSQLArray($sqlarray);


$query = 'ALTER TABLE '.cms_db_prefix().'module_oscs_rapport_tmp ADD INDEX (cni)';
if ($db->Execute($query) === false)
{
	die('erreur grave durant l\'installation 6');
}

// create a sequence
$db->CreateSequence(cms_db_prefix()."module_oscs_rapport_tmp_seq");


// create a permission
$this->CreatePermission('Set Open Statistics Community Server Prefs','OSC Server : Set Prefs');

# Setup display template
$fn = dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'default.tpl';
if(file_exists( $fn ))
{
    $template = @file_get_contents($fn);
    $this->SetPreference('default_default_template_contents',$template);
    $this->SetTemplate('defaultSample',$template);
    $this->SetPreference('current_default_template','Sample');
}
$fn = dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'frontoffice.tpl';
if(file_exists( $fn ))
{
    $template = @file_get_contents($fn);
    $this->SetPreference('default_frontoffice_template_contents',$template);
    $this->SetTemplate('frontofficeSample',$template);
    $this->SetPreference('current_frontoffice_template','Sample');
}
$fn = dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'showListeModule.tpl';
if(file_exists( $fn ))
{
    $template = @file_get_contents($fn);
    $this->SetPreference('default_showListeModule_template_contents',$template);
    $this->SetTemplate('showListeModuleSample',$template);
    $this->SetPreference('current_showListeModule_template','Sample');
}
$fn = dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'showTop.tpl';
if(file_exists( $fn ))
{
    $template = @file_get_contents($fn);
    $this->SetPreference('default_showTop_template_contents',$template);
    $this->SetTemplate('showTopSample',$template);
    $this->SetPreference('current_showTop_template','Sample');
}
$fn = dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'vueDetail.tpl';
if(file_exists( $fn ))
{
    $template = @file_get_contents($fn);
    $this->SetPreference('default_vueDetail_template_contents',$template);
    $this->SetTemplate('vueDetailSample',$template);
    $this->SetPreference('current_vueDetail_template','Sample');
}

// put mention into the admin log
$this->Audit( 0, 
	      $this->Lang('friendlyname'), 
	      $this->Lang('installed', $this->GetVersion()) );
?>