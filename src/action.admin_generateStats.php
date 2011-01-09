<?php

if (!isset($gCms)) exit;

// Verification de la permission
if (! $this->CheckPermission('Set Open Statistics Community Server Prefs')) 
  return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));

 if(!isset($params['mois']) || !is_numeric($params['mois']))
  return $this->DisplayErrorPage($id, $params, $returnid,"hack");
  
// Liste des rapports du mois dans leur derniere version
$query =  'SELECT max(id) as id, user_id FROM  '.cms_db_prefix().'module_oscs_rapport r ';
$query .= ' WHERE DATE_FORMAT(date_reception, \'%Y%m\') = ? group by user_id';
$result = $db->Execute($query,array($params['mois']));


if ($result === false)
{
	echo $query."<br/>".$params['mois'];
	echo "Database error durant la r&eacute;cup&eacute;ration des ID de rapports!";
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
	echo "Database error durant la r&eacute;cup&eacute;ration des contenus de rapports!";
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
	//Recuperation des versions
	if(isset($arrayRapport['cms_version']))
	{
		$syntheseCmsVersionCount++;
		if(!isset($tab_version[$arrayRapport['cms_version']]))
			$tab_version[$arrayRapport['cms_version']] = 1;
		else
			$tab_version[$arrayRapport['cms_version']]++;
	}
	
	//Recuperation des modules
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
	
	//Limite memoire
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

//Tri des differents tableau
ksort($tab_version);
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



$query3 = 'SELECT texte, date_traduction as date FROM '.cms_db_prefix().'module_oscs_traduction order by date_traduction ASC';
$result3 = $db->Execute($query3);
if ($result3 === false){echo "Database error durant la r&eacute;cup&eacute;ration des outils de traduction!";	exit;}
$listeTraduction = array();
$lastTraduction = null;
while ($row = $result3->FetchRow())
{
	$listeTraduction[$row['date']] = unserialize($row['texte']);
	$lastTraduction = $listeTraduction[$row['date']];
}
	

$admintheme =& $gCms->variables['admintheme'];
foreach($tab_module as $key => $element)
{
	$class = new stdClass;
	$class->name = $key;
	$class->count = $element['cpt'];
	$class->percent = round(($syntheseCmsModuleCount == 0?0:($element['cpt'] * 100 / $syntheseCmsModuleCount)),2).'%';
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
	
	//changement du nom eventuellement
	$searchName = array("DownCnt");
	$remplaceName = array("DownloadCounter");
	$nameTraduit = str_replace($searchName, $remplaceName, $class->name);

	//recuperation des dernieres traduction
	if(isset($lastTraduction[$nameTraduit]))
	{
		$class->traductionRealisee = $lastTraduction[$nameTraduit]->done;
		$class->traductionTotale = $lastTraduction[$nameTraduit]->total;
		if($lastTraduction[$nameTraduit]->total == 0)
		{
			$class->traductionPourcent = 0;
		}
		else
		{
			$class->traductionPourcent = floor($lastTraduction[$nameTraduit]->done*100/$lastTraduction[$nameTraduit]->total);
		}
	}
	else
	{
		$class->traductionRealisee = 0;
		$class->traductionTotale = 0;
		$class->traductionPourcent = 0;
	}
	
	$class->traduction = new stdclass();	
	$class->traduction->line1 = array();
	$class->traduction->line2 = array();
	
	
	$i = 0;
	foreach ($listeTraduction as $traduction)
	{
		if(isset($traduction[$nameTraduit]))
		{
			$class->traduction->line1[$i] = $traduction[$nameTraduit]->done;
			$class->traduction->line2[$i] = $traduction[$nameTraduit]->total;
		}
		$i++;
	}

	if(isset($class->traduction))
	{	
		
		$tradline1 = "";
		$tradline2 = "";
		for($i = 0; $i<count($class->traduction->line1); $i++)
		{
			if($i != 0)
			{
				$tradline1 .= ",";
				$tradline2 .= ",";
			}
			$tradline1 .= '['.($i+1).','.$class->traduction->line1[$i].']';
			$tradline2 .= '['.($i+1).','.($class->traduction->line2[$i]-$class->traduction->line1[$i]).']';
		}
		
		
		$class->traduction->line1 = $tradline1;
		$class->traduction->line2 = $tradline2;
		
		
	
		//die(print_r($class->traduction->line1).print_r($class->traduction->line2));
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


//Donnees JS
$line1 = getJSLine($syntheseCmsVersion);
$line2 = getJSLine($synthesePhp);
$line3 = getJSLine($syntheseMemoryLimit);
$line4 = getJSLine($syntheseSafeMode);

//Sauvegarde des stats generees
$handle = fopen('..'. DIRECTORY_SEPARATOR .'modules'. DIRECTORY_SEPARATOR .'OpenStatisticsCommunityServer'. DIRECTORY_SEPARATOR .'stats'. DIRECTORY_SEPARATOR .$params['mois'],'w+');
if (!$handle) 
  return $this->DisplayErrorPage($id, $params, $returnid, "&eacute;criture statistiques impossible");

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