<?php
#-------------------------------------------------------------------------
# Module: OSCS - XXX
#
# Version: de Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : contact [plop] furie [plap] be
#
# The module's download page is : N/A
# The module's demo page is : http://www.cmsmadesimple.fr/statistiques
#
# The discussion page around the module : http://www.cmsmadesimple.fr/forum/viewtopic.php?id=2908
# The author's GIT page is : https://github.com/besstiolle
# The module's GIT page is : https://github.com/besstiolle/Open-Statistics-Community-Server
# The module's SVN page is : N/A
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2004-2011 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#-------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#-------------------------------------------------------------------------

$cgextensions = cms_join_path($gCms->config['root_path'],'modules',
			      'CGExtensions','CGExtensions.module.php');
if( !is_readable( $cgextensions ) )
{
  echo '<h1><font color="red">ERROR: The CGExtensions module could not be found.</font></h1>';
  return;
}
require_once($cgextensions);

class OpenStatisticsCommunityServer extends CGExtensions
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
    return '0.0.6';
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
    return 'contact@furie.be';
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
    return array('CGExtensions'=>'1.21.3');
  }

  function MinimumCMSVersion()
  {
    return "1.9.1";
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
	
	$this->RegisterRoute('/statistiques\/modules\/(?P<returnid>[0-9]+)\/(?P<mod>[a-zA-Z_\- ,]+)\/(?P<mois>[0-9]+)$/',
		 array('action'=>'showModule'));
		 
	$this->RegisterRoute('/statistiques\/modules\/(?P<returnid>[0-9]+)\/(?P<mois>[0-9]+)$/',
		 array('action'=>'showListeModule'));
		 
	
	$this->RestrictUnknownParams();
	
	$this->CreateParameter('template', null, 'todo');
	$this->SetParameterType('template',CLEAN_STRING);
	
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
	
	function _getMois($mois)
	{
		global $listeCache;
		
		//Si aucun mois definit
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
