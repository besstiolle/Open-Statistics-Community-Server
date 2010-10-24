<?php
class OpenStatisticsCommunityServer extends CMSModule
{
   var $listeCache = null;
   var $folder = null;

  function GetName()
  {
    return get_class($this);
  }

  function GetFriendlyName()
  {
    return $this->Lang('friendlyname');
  }

  function GetVersion()
  {
    return '0.0.5';
  }
  
  function GetHelp()
  {
    return $this->Lang('help');
  }
  
  function GetAuthor()
  {
    return 'Kevin Danezis (Bess)';
  }

  function GetAuthorEmail()
  {
    return 'besstiolle@gmail.com';
  }
  
  function GetChangeLog()
  {
    return $this->Lang('changelog');
  }
  
  /**
   * IsPluginModule()
   * @return bool True if this module can be included in page and or template
   */
  function IsPluginModule()
  {
    return true;
  }

  function HasAdmin()
  {
    return true;
  }

  function GetAdminSection()
  {
    return 'extensions';
  }

  function GetAdminDescription()
  {
    return $this->Lang('moddescription');
  }

  function VisibleToAdminUser()
  {
    return true;
  }
  
  function GetDependencies()
  {
    return array();
  }

  function MinimumCMSVersion()
  {
    return "1.6.7";
  }
  
  function HandlesEvents()
  {
		return true;
  }
  
  function DoEvent($originator, $eventname, &$params)
	{
		$content = $params["content"];
		
		if(strpos($content, ".tablesorter(") === false && strpos($content, ".jqplot(") === false)
		{
			return;
		}
		
		global $gCms;
		$config = $gCms->GetConfig();
		
		$scriptJquery = '
<script language="javascript" type="text/javascript" src="'.$config['root_url'].'/modules/OpenStatisticsCommunityServer/js/jquery.js"></script>
		';	
		
		$scriptTableSorter = '
<script language="javascript" type="text/javascript" src="'.$config['root_url'].'/modules/OpenStatisticsCommunityServer/js/tablesorter/jquery.tablesorter.min.js"></script>
<script language="javascript" type="text/javascript" src="'.$config['root_url'].'/modules/OpenStatisticsCommunityServer/js/tablesorter/jquery.tablesorter.pager.js"></script>
		';	
		$scriptJplot = '			  
<!--[if IE]>
<script language="javascript" type="text/javascript" src="'.$config['root_url'].'/modules/OpenStatisticsCommunityServer/js/jplot/excanvas.min.js"></script>
<![endif]-->
<script language="javascript" type="text/javascript" src="'.$config['root_url'].'/modules/OpenStatisticsCommunityServer/js/jplot/jquery.jqplot.js"></script>
<script language="javascript" type="text/javascript" src="'.$config['root_url'].'/modules/OpenStatisticsCommunityServer/js/jplot/plugins/jqplot.pieRenderer.js"></script>
<link   rel="stylesheet"      type="text/css"        href="'.$config['root_url'].'/modules/OpenStatisticsCommunityServer/js/jplot/jquery.jqplot.css" />
';		
		$script = $scriptJquery;
		if(strpos($content, ".tablesorter(") !== false)
			$script .= $scriptTableSorter;
		
		if(strpos($content, ".jqplot(") !== false)
			$script .= $scriptJplot;
		
		$script = trim($script);
		
		if (function_exists('str_ireplace'))
			$params["content"] = str_ireplace('<head>', "<head>\n$script\n", $content);
		else
			$params["content"] = eregi_replace('<head>', "<head>\n$script\n", $content);
	}
  
  function SetParameters()
  {
	$this->RegisterModulePlugin();
	
	$this->RegisterRoute('/statistiques\/(?P<returnid>[0-9]+)\/(?P<mois>[0-9]+)$/',
		 array('action'=>'default'));
		 
	$this->RegisterRoute('/statistiques\/top\/(?P<returnid>[0-9]+)\/(?P<mois>[0-9]+)$/',
		 array('action'=>'showTop'));
	
	$this->RegisterRoute('/statistiques\/modules\/(?P<returnid>[0-9]+)\/(?P<mod>[a-zA-Z_ ,]+)\/(?P<mois>[0-9]+)$/',
		 array('action'=>'showModule'));
		 
	$this->RegisterRoute('/statistiques\/modules\/(?P<returnid>[0-9]+)\/(?P<mois>[0-9]+)$/',
		 array('action'=>'showListeModule'));
		 
	
	$this->RestrictUnknownParams();
	
	$this->CreateParameter('mois', 201009, 'todo');
	$this->SetParameterType('mois',CLEAN_INT);
	
	
	$this->CreateParameter('mod', null, 'todo');
	$this->SetParameterType('mod',CLEAN_STRING);
	
	$this->CreateParameter('none', null, 'todo');
	$this->SetParameterType('none',CLEAN_STRING);
	
  }
  
  function InstallPostMessage()
  {
    return $this->Lang('postinstall',$this->GetVersion());
  }

  function UninstallPostMessage()
  {
    return $this->Lang('postuninstall');
  }
  
  function UninstallPreMessage()
  {
    return $this->Lang('really_uninstall');
  }
   
	function _dbToDate($stringDate)
	{
		return mktime(substr($stringDate, 11,2),
					substr($stringDate, 14,2),
					substr($stringDate, 17,2),
					substr($stringDate, 5,2),
					substr($stringDate, 8,2),
					substr($stringDate, 0,4));
	}
  
	function _getTimeForDB($db)
	{
		return trim($db->DBTimeStamp(time()), "'");
	}
	
	function _GenerationCle($Texte,$CleDEncryptage)
	{
	  $CleDEncryptage = md5($CleDEncryptage);
	  $Compteur=0;
	  $VariableTemp = "";
	  for ($Ctr=0;$Ctr<strlen($Texte);$Ctr++)
		{
		if ($Compteur==strlen($CleDEncryptage))
		  $Compteur=0;
		$VariableTemp.= substr($Texte,$Ctr,1) ^ substr($CleDEncryptage,$Compteur,1);
		$Compteur++;
		}
	  return $VariableTemp;
	}

	function _Decrypte($Texte,$Cle)
	{
	  $Texte = $this->_GenerationCle(base64_decode($Texte),$Cle);
	  $VariableTemp = "";
	  for ($Ctr=0;$Ctr<strlen($Texte);$Ctr++)
		{
		$md5 = substr($Texte,$Ctr,1);
		$Ctr++;
		$VariableTemp.= (substr($Texte,$Ctr,1) ^ $md5);
		}
	  return $VariableTemp;
	} 
	
	function _getAllCache()
	{
		global $listeCache;
		global $folder;
		
		$folder_tmp = DIRECTORY_SEPARATOR .'modules'. DIRECTORY_SEPARATOR .'OpenStatisticsCommunityServer'. DIRECTORY_SEPARATOR .'stats';
		if(is_dir('.'.$folder_tmp))
			$folder = '.'.$folder_tmp;
		else if(is_dir('..'.$folder_tmp))
			$folder = '..'.$folder_tmp;
		else
			die("erreur grave : aucun r&eacute;pertoire ne correspond :/");
				
		$dossier = opendir($folder);
		
		$listeCache = array();
		while ($fichier = readdir($dossier)) {
		  if ($fichier != "." && $fichier != "..") 
			$listeCache[] = $fichier;
		}
		rsort($listeCache);
		closedir($dossier);
		
		return $listeCache;
	}
	
	function _getCacheByMonthToArray($mois)
	{
		global $folder;
		
		$filename = $folder.DIRECTORY_SEPARATOR.$mois;
		$handle = fopen($filename,'r');
		$contentstats = fread($handle, filesize($filename));
		fclose($handle);

		return explode('|||',$contentstats);
	}
	
	function _getFrontOffice()
	{
		global $gCms;
		$db = &$gCms->GetDb();
		$smarty = &$gCms->GetSmarty();

		$query = 'SELECT count(*) as cpt FROM '.cms_db_prefix().'module_oscs_user';
		$cpt = $db->getOne($query);
		$smarty->assign('cptUser',$cpt);

		$query = 'SELECT count(*) as cpt FROM '.cms_db_prefix().'module_oscs_rapport';
		$cpt = $db->getOne($query);
		$smarty->assign('cptRapport',$cpt);

		$query = 'SELECT min(date_reception) as date_reception FROM '.cms_db_prefix().'module_oscs_rapport';
		$cpt = $db->getOne($query);
		$smarty->assign('min_date_reception',$cpt);

		return $this->ProcessTemplate('frontoffice.tpl');
	}
	
	function _getMois($mois)
	{
		global $listeCache;
		
		//Si aucun mois définit
		if(!isset($mois))
		{
			$mois = $listeCache[0];
		} else
		{
			$existe = false;
			foreach($listeCache as $fichier)
			{	
				if($mois == $fichier)
				{
					$existe = true;
					break;
				}
			}
			if(!$existe)
			{
				$mois = null;
			}
		}
		
		return $mois;
	}
	
	function _getMoisLiteral($mois)
	{
		$array = array("01"=>'janvier', 
						"02"=>'f&eacute;vrier', 
						"03"=>'mars', 
						"04"=>'avril', 
						"05"=>'mai', 
						"06"=>'juin', 
						"07"=>'juillet', 
						"08"=>'aout', 
						"09"=>'septembre', 
						"10"=>'octobre',
						"11"=>'novembre', 
						"12"=>'d&eacute;cembre');
		
		$an = substr($mois, 0,4);		
		$mois = substr($mois,4);
		
		return $array[$mois] . " " . $an;
	}
	
  
}
?>
