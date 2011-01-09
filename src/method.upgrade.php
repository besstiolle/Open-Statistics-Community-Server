<?php


if (!isset($gCms)) exit;


$db =& $gCms->GetDb();

$current_version = $oldversion;

switch($current_version)
{
  case '0.0.1':
  case '0.0.2':
  case '0.0.3':
  case '0.0.4':

	$taboptarray = array( 'mysql' => 'TYPE=MyISAM' );
	$dict = NewDataDictionary( $db );
	$flds = "
		 texte X,
		 date_traduction " . CMS_ADODB_DT . "
	";
	$sqlarray = $dict->CreateTableSQL( cms_db_prefix()."module_oscs_traduction",
					   $flds, 
					   $taboptarray);
	$dict->ExecuteSQLArray($sqlarray);

  case '0.0.5':

	$this->AddEventHandler('Core', 'ContentPostRender', false);

  case '0.0.6':

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
}

?>