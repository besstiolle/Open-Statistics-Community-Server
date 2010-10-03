<?php
#-------------------------------------------------------------------------
# Module: Open Statistics Community Server - Enregistreur de rapport de statistiques 
#		  pour Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/rts-client.html
# Version: 0.0.1, Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : besstiolle [plop] gmail [plap] com
# Method: action.admin_showRapport
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://dev.cmsmadesimple.org/projects/shootbox/
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
if (!isset($gCms)) exit;

// Vérification de la permission
if (! $this->CheckPermission('Set Open Statistics Community Server Prefs')) 
  return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));

 if(!isset($params['mois']) || !is_numeric($params['mois']))
  return $this->DisplayErrorPage($id, $params, $returnid,"hack");
  
// Liste des rapports du mois dans leur dernière version
$query =  'SELECT max(id) as id, user_id FROM  '.cms_db_prefix().'module_oscs_rapport r ';
$query .= ' WHERE DATE_FORMAT(date_reception, \'%Y%m\') = ? group by user_id';
$result = $db->Execute($query,array($params['mois']));


if ($result === false)
{
	echo $query."<br/>".$params['mois'];
	echo "Database error durant la récupération des ID de rapports!";
	exit;
}
$listeId = $result->GetArray();
$chaine = "";
foreach($listeId as $element)
{
	if($chaine != "")
		$chaine .= ", ";
		
	$chaine .= $element['id'];
}

$query2 = 'SELECT u.cni, u.clepublic, r.rapport FROM '.cms_db_prefix().'module_oscs_rapport r, '.cms_db_prefix().'module_oscs_user u where r.id in ('.$chaine.') and u.id=r.user_id';
$result2 = $db->Execute($query2);

if ($result2 === false)
{
	echo $query2."<br/>".$chaine;
	echo "Database error durant la récupération des contenus de rapports!";
	exit;
}


$syntheseCmsVersion = array();
$syntheseCmsVersionCount = 0;
$tab_version = array();

$syntheseCmsModule = array();
$syntheseCmsModuleCount = 0;
$tab_module = array();

$synthesePhp = array();
$synthesePhpCount = 0;
$tab_php = array();

$syntheseMemoryLimit = array();
$syntheseMemoryLimitCount = 0;
$tab_memoryLimit = array();

$syntheseSafeMode = array();
$syntheseSafeModeCount = 0;
$tab_safeMode = array();





while ($row = $result2->FetchRow())
{
	$arrayRapport = unserialize($this->_Decrypte($row['rapport'], $row['clepublic']));
	//Récupération des versions
	if(isset($arrayRapport['cms_version']))
	{
		$syntheseCmsVersionCount++;
		if(!isset($tab_version[$arrayRapport['cms_version']]))
			$tab_version[$arrayRapport['cms_version']] = 1;
		else
			$tab_version[$arrayRapport['cms_version']]++;
	}
	
	//Récupération des modules
	if(isset($arrayRapport['installed_modules']))
	{
		$syntheseCmsModuleCount++;
		foreach($arrayRapport['installed_modules'] as $module)
		{	
			if($module['status'] != "installed")
			{
				continue;
			}
			$var = $module['module_name'];
			if(!isset($tab_module[$var]))
			{
				$tab_module[$var]['cpt'] = 1;
				$tab_module[$var]['version'] = array();
			}
			$tab_module[$var]['cpt']++;
			
			
			$version = $module['version'];
			if(!isset($tab_module[$var]['version'][$version]))
			{
				$tab_module[$var]['version'][$version] = 1;
			}
			$tab_module[$var]['version'][$version]++;
			
		}
	}
	
	//Version php
	if(isset($arrayRapport['php_information']['phpversion']))
	{
		$synthesePhpCount++;
		$var = $arrayRapport['php_information']['phpversion']['value'];
		$pos = strpos($var,"-");
		if($pos !== FALSE)
		{
			$var = substr($var,0,$pos);
		}
		if(!isset($tab_php[$var]))
		{
			$tab_php[$var] = 0;
		}
		$tab_php[$var] ++;
	}
	
	//Limite mémoire
	if(isset($arrayRapport['php_information']['memory_limit']))
	{
		$syntheseMemoryLimitCount++;
		$var = $arrayRapport['php_information']['memory_limit']['value'];
		if(!isset($tab_memoryLimit[$var]))
		{
			$tab_memoryLimit[$var] = 0;
		}
		$tab_memoryLimit[$var] ++;
	}
	
	//Safe Mode
	if(isset($arrayRapport['php_information']['safe_mode']))
	{
		$syntheseSafeModeCount++;
		$var = trim($arrayRapport['php_information']['safe_mode']['value']);
		if(!isset($tab_safeMode[$var]))
		{
			$tab_safeMode[$var] = 0;
		}
		$tab_safeMode[$var] ++;
	}
}

//Tri des différents tableau
ksort($tab_php);
ksort($tab_memoryLimit, SORT_NUMERIC);
ksort($tab_safeMode);

//Transformation en tableau d'objet
foreach($tab_version as $key => $element)
{
	$class = new stdClass;
	$class->name = $key;
	$class->count = $element;
	$class->percent = round(($syntheseCmsVersionCount == 0?0:($element * 100 / $syntheseCmsVersionCount)),2).'%';
	$syntheseCmsVersion[] = $class;
}



$query3 = 'SELECT texte, date_traduction as date FROM '.cms_db_prefix().'module_oscs_traduction order by date_traduction DESC';
$result3 = $db->Execute($query3);
if ($result3 === false){echo "Database error durant la récupération des outils de traduction!";	exit;}
$listeTraduction = array();
while ($row = $result3->FetchRow())
{
	$listeTraduction[$row['date']] = unserialize($row['texte']);
}
	


$admintheme =& $gCms->variables['admintheme'];
foreach($tab_module as $key => $element)
{
	$class = new stdClass;
	$class->name = $key;
	$class->count = $element['cpt'];
	$class->percent = round(($syntheseCmsModuleCount == 0?0:($element['cpt'] * 100 / $syntheseCmsModuleCount)),2).'%';
	$class->traductionRealisee = 0;
	$class->traductionTotale = 0;
	$class->traductionPourcent = 0;
	$syntheseCmsModule[] = $class;
	
	$class->version = array();
	foreach($element['version'] as $key2 => $element2)
	{
		$class2 = new stdClass;
		$class2->name = $key2;
		$class2->count = $element2;
		$class2->percent = round(($element['cpt'] == 0?0:($element2 * 100 / $element['cpt'])),2).'%';
		$class->version[] = $class2;
	}
	$class->versionLine = getJSLinePourcent($class->version);
	
	$isfirst = true;
	$i = 0;
	$searchName = array("DownCnt");
	$remplaceName = array("DownloadCounter");
	foreach ($listeTraduction as $traduction)
	{
		$nameTraduit = str_replace($searchName, $remplaceName, $class->name);
		if(isset($traduction[$nameTraduit]))
		{
			if($isfirst)
			{
				$isfirst = false;
				$class->traductionRealisee = $traduction[$nameTraduit]->done;
				$class->traductionTotale = $traduction[$nameTraduit]->total;
				$class->traductionPourcent = floor($traduction[$nameTraduit]->done*100/$traduction[$nameTraduit]->total);
				$class->traduction = new stdclass();	
				$class->traduction->line1 = array();
				$class->traduction->line2 = array();
			}
			$class->traduction->line1[$i] = $traduction[$nameTraduit]->done;
			$class->traduction->line2[$i] = $traduction[$nameTraduit]->total;
		}
		$i++;
	}

	if(isset($class->traduction))
	{
		rsort($class->traduction->line1);
		rsort($class->traduction->line2);
		
		$tradline1 = "";
		$tradline2 = "";
		for($i = 0; $i<count($class->traduction->line1); $i++)
		{
			if($i != 0)
			{
				$tradline1 .= ",";
				$tradline2 .= ",";
			}
			$tradline1 .= '['.$i.','.$class->traduction->line1[$i].']';
			$tradline2 .= '['.$i.','.($class->traduction->line2[$i]-$class->traduction->line1[$i]).']';
		}
		
		$class->traduction->line1 = $tradline1;
		$class->traduction->line2 = $tradline2;
	}	
	
}
foreach($tab_php as $key => $element)
{
	$class = new stdClass;
	$class->name = $key;
	$class->count = $element;
	$class->percent = round(($synthesePhpCount == 0?0:($element * 100 / $synthesePhpCount)),2).'%';
	$synthesePhp[] = $class;
}
foreach($tab_memoryLimit as $key => $element)
{
	$class = new stdClass;
	$class->name = $key;
	$class->count = $element;
	$class->percent = round(($syntheseMemoryLimitCount == 0?0:($element * 100 / $syntheseMemoryLimitCount)),2).'%';
	$syntheseMemoryLimit[] = $class;
}
foreach($tab_safeMode as $key => $element)
{
	$class = new stdClass;
	$class->name = $key;
	$class->count = $element;
	$class->percent = round(($syntheseSafeModeCount == 0?0:($element * 100 / $syntheseSafeModeCount)),2).'%';
	$syntheseSafeMode[] = $class;
}


//Données JS
$line1 = getJSLine($syntheseCmsVersion);
$line2 = getJSLine($synthesePhp);
$line3 = getJSLine($syntheseMemoryLimit);
$line4 = getJSLine($syntheseSafeMode);

//Sauvegarde des stats générées
$handle = fopen('..'. DIRECTORY_SEPARATOR .'modules'. DIRECTORY_SEPARATOR .'OpenStatisticsCommunityServer'. DIRECTORY_SEPARATOR .'stats'. DIRECTORY_SEPARATOR .$params['mois'],'w+');
if (!$handle) 
  return $this->DisplayErrorPage($id, $params, $returnid, "écriture statistiques impossible");

fwrite($handle, serialize($syntheseCmsVersion));
fwrite($handle, "|||");
fwrite($handle, serialize($line1));
fwrite($handle, "|||");
fwrite($handle, serialize($syntheseCmsModule));
fwrite($handle, "|||");
fwrite($handle, serialize($synthesePhp));
fwrite($handle, "|||");
fwrite($handle, serialize($line2));
fwrite($handle, "|||");
fwrite($handle, serialize($syntheseMemoryLimit));
fwrite($handle, "|||");
fwrite($handle, serialize($line3));
fwrite($handle, "|||");
fwrite($handle, serialize($syntheseSafeMode));
fwrite($handle, "|||");
fwrite($handle, serialize($line4));
fwrite($handle, "|||");

fclose($handle);

$backlink = $this->CreateLink($id, 'default', $returnid, 'ici', array("mois"=>"201009"));
 
echo "op&eacute;ration termin&eacute;e. Cliquez $backlink pour voir le r&eacute;sultat.";
  
 return;

/*
  
$smarty->assign('syntheseCmsVersion',$syntheseCmsVersion);
$smarty->assign('syntheseCmsModule',$syntheseCmsModule);
$smarty->assign('synthesePhp',$synthesePhp);
$smarty->assign('syntheseMemoryLimit',$syntheseMemoryLimit);
$smarty->assign('syntheseSafeMode',$syntheseSafeMode);
$smarty->assign('line1',$line1);
$smarty->assign('line2',$line2);
$smarty->assign('line3',$line3);
$smarty->assign('line4',$line4);
$smarty->assign('mois',substr($params['mois'],4,2).'/'.substr($params['mois'],0,4));
$smarty->assign('localUrl', htmlspecialchars($_SERVER['REQUEST_URI']));

$admintheme =& $gCms->variables['admintheme'];
$backlink = $this->CreateLink($id, 'defaultadmin', $returnid, $admintheme->DisplayImage('icons/system/back.gif', $this->Lang('back'),'','','systemicon'), array("active_tab"=>"generation"));
$this->smarty->assign('backlink',$backlink);
	  
$smarty->assign_by_ref('module',$this);

  


echo $this->ProcessTemplate('admindetailstats.tpl');
*/
function getJSLine($liste)
{
	$line = "";
	foreach($liste as $element)
	{
		if($line != "")
			$line .= ", ";
		$line .= "['$element->name', $element->count]";
	}
	$line = "[".$line."];";
	return $line;
}
function getJSLinePourcent($liste)
{
	$line = "";
	foreach($liste as $element)
	{
		if($line != "")
			$line .= ", ";
		$line .= "['$element->name => $element->percent', $element->count]";
	}
	$line = "[".$line."];";
	return $line;
}

?>