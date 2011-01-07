<?php

require dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME']))) . DIRECTORY_SEPARATOR . 'include.php';

$db =& $gCms->GetDb();
$oscs =& $gCms->modules["OpenStatisticsCommunityServer"]['object'];

$reponse = array (
	0 => "OK",
	100 => "Identification KO",
	200 => "CNI incorrecte ou inconnue",
	201 => "Corruption des donn&eacute;es transf&eacute;r&eacute;es",
	202 => "Tentative d'usurpation d'identit&eacute;",
	203 => "Taille 20ko exc&eacute;d&eacute;e",
	204 => "Data not found",
	500 => "Serveur indisponible pour une dur&eacute;e indetermin&eacute;e",
	501 => "Serveur indisponible pour une courte dur&eacute;e",
	503 => "Erreur interne inatendue"
);	

function giveResponse($codeRetour)
{
	global $reponse;
	die($codeRetour."|".$reponse["$codeRetour"]);
}

function isReportValide($report)
{	
	return (isset($report) && is_array($report) && count($report) != 0);
}

function testVariable()
{
	 /**      CNI | RESUME | SIZE | DATA   **/
	 /**    X(90) |  X(32) | I(5) | X(20K)   **/

	//Variable ID : le code identifiant de l'utilisateur = 50 Caracteres
	if(!isset($_GET['CNI']) || empty($_GET['CNI']) || strlen($_GET['CNI']) != 50)
	{
		giveResponse(100);
	}

	//Variable resume : [ CNI X(50) + DATA ] ==> md5 X(32)
	if(!isset($_GET['RESUME']) || empty($_GET['RESUME']) || strlen($_GET['RESUME']) != 32)
	{
		echo "resume null<br/>";
		giveResponse(201);
	}

	//Variable size : not null
	if(!isset($_GET['SIZE']) || empty($_GET['SIZE']) || strlen($_GET['SIZE']) >= 5)
	{
		echo "size null<br/>";
		giveResponse(201);
	}

	$size = intval($_GET['SIZE']);

	//Variable multi-envois : not null
	if(!isset($_GET['new']) || empty($_GET['new']) || !is_numeric($_GET['new']))
	{
		if(!isset($_GET['packet']) || empty($_GET['packet']) || !is_numeric($_GET['packet'])
			|| !isset($_GET['partdata']) || empty($_GET['partdata'])
			|| !isset($_GET['sid']) || empty($_GET['sid']) || !is_numeric($_GET['sid']))
		{
			
			echo "combo null<br/>";
			giveResponse(201);
		}
	}
}

function getUser($cni)
{
	global $db;
	// Recuperer le User et ses cles
	$query = 'SELECT * FROM '.cms_db_prefix().'module_oscs_user WHERE cni = ?';
		
	$result = $db->Execute($query,array($cni));
	if ($result === false){die("Database error durant la recherche de l'utilisateur!");}

	$user = null;
	while ($row = $result->FetchRow())
	{
		//Si plusieurs utilisateurs existent c'est anormal
		if($user != null)
			giveResponse(503);
		
		$user = $row;
	}
	
	//Si utilisateur inconnu
	if(!isset($user))
		giveResponse(200);
		
	return $user;
}

testVariable();

$cni = $_GET['CNI'];
$resume = $_GET['RESUME'];
$size = $_GET['SIZE'];

if(isset($_GET['new']))
{
	$nbPart = $_GET['new'];
	
	//Verif de l'utilisateur
	$user = getUser($cni);
	
	//Supression de l'existant de l'utilisateur dans tmp pour eviter le flood
	$query = 'DELETE FROM '.cms_db_prefix().'module_oscs_rapport_tmp WHERE cni = ?';
	$param = array($cni);
	$result = $db->Execute($query,$param);
	if ($result === false){die("Database error durant la suppression des lignes de l'auteur!");}
	
	$tab = array();
	//Insert du nouveau tableau
	$queryInsert = 'INSERT INTO '.cms_db_prefix().'module_oscs_rapport_tmp (id, cni, resume, size, nbpart ,data) values (?,?,?,?,?,?)';
	$sid = $db->GenID(cms_db_prefix().'module_oscs_rapport_tmp_seq');
	$param = array($sid, $cni, $resume, $size, $nbPart, serialize($tab));
	$result = $db->Execute($queryInsert, $param);
	if ($result === false){die("Database error durant l'insert de la premi&egrave;re donn&eacute;e!");}
	echo $sid; //On renvoi le SID de la ligne
	die();
} else 
{
	$packet = $_GET['packet'];
	$partdata = $_GET['partdata'];
	$sid = $_GET['sid'];
	
	//Verif de l'utilisateur
	$user = getUser($cni);
	
	//Recuperation du tableau en base
	$query = 'SELECT * from '.cms_db_prefix().'module_oscs_rapport_tmp WHERE id = ?';
	$param = array($sid);
	$result = $db->Execute($query,$param);
	if ($result === false){die("Database error durant la r&eacute;cup&eacute;ration des lignes de l'auteur!");}
	$res = null;
	while ($row = $result->FetchRow())
	{
		//Si plusieurs tableau existent c'est anormal
		if($res != null)
			giveResponse(503);
		
		$res = $row;
	}
	
	//Si aucune ligne remontee
	if(!isset($res)){giveResponse(204);}
	
	$data = unserialize($res['data']);
	if($packet<=0 || $packet > $res['nbpart'])
	{
		echo '$packet<=0 || $packet > $res["nbpart"]<br/>';
		echo $res['nbpart']."anark<br/>";
		giveResponse(201);
	}
	
	$data[$packet] = $partdata;
	
//	echo $packet."go on".$res['nbpart'];
	
	//Si un Neme envois, on enregistre et on sort
	if($packet != $res['nbpart'])
	{
		$queryInsert = 'Update '.cms_db_prefix().'module_oscs_rapport_tmp set data=? WHERE id = ?';
		$param = array(serialize($data), $sid);
		$result = $db->Execute($queryInsert, $param);
		if ($result === false){die("Database error durant l'update de la donn&eacute;e!");}
		echo 0;
		die();
	}
	
//	echo  print_r($data)."<br/>";
	
	$mydata = '';
	for($i = 1; $i <= $packet; $i++)
	{
//		echo strlen($data[$i]).'  -<br/>';
		$mydata .= $data[$i];
	}
	$data = $mydata;
	
	
	//Comparaison size recu et size attendue
	if(strlen($data) != $size)
	{
		echo "<br/>4 - attendu :  ".$size." recu : ".strlen($data)."<br/>";
		giveResponse(201);
	}

	//Controle de coherence entre le MD5 suppose et le MD5 obtenu
	if(md5($data) != $resume)
	{
	//	echo $data."\n\n<br/><br/>";
		echo "5 - attendu :  ".$resume." recu : ".md5($data);
		giveResponse(201);
	}

	$cleDecryptage = $user['clepublic'];

	$report = $oscs->_Decrypte($data, $cleDecryptage);
	$report = unserialize($report);


	//Si tentative d'insertion de fausses donnees
	if(!isReportValide($report))
		giveResponse(202);
			
	//Arrive a ce niveau on enregistre les resultats
	$queryInsert = 'INSERT INTO '.cms_db_prefix().'module_oscs_rapport (id, user_id, reponse, rapport, date_reception) values (?,?,?,?,?)';

	$sid = $db->GenID(cms_db_prefix().'module_oscs_rapport_seq');
	$time = $oscs->_getTimeForDB($db);

	$param = array($sid, $user['id'], 0, $data,  $time);
	$result = $db->Execute($queryInsert, $param);

	if ($result === false){die("Database error durant l'insert!");}

	//On met egalement a jour la date de dernier envoi de rapport pour l'utilisateur
	$query = 'UPDATE '.cms_db_prefix().'module_oscs_user SET date_update = ? WHERE cni = ?';

	$param = array($time, $_GET['CNI']);
	$result = $db->Execute($query,$param);

	if ($result === false){die("Database error durant la mise &agrave; jour utilisateur!");}

	//Supression de l'existant de l'utilisateur dans tmp pour eviter l'encombrement de la bdd temp
	$query = 'DELETE FROM '.cms_db_prefix().'module_oscs_rapport_tmp WHERE cni = ?';
	$param = array($cni);
	$result = $db->Execute($query,$param);
	if ($result === false){die("Database error durant la suppression des lignes de l'auteur!");}

	echo 0;
	die();
}

	
?>