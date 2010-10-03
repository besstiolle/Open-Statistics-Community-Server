<?php
#-------------------------------------------------------------------------
# Module: Open Statistics Community Server - Enregistreur de rapport de statistiques 
#		  pour Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/rts-client.html
# Version: 0.0.1, Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : besstiolle [plop] gmail [plap] com
# Method: ajax.askCNI
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
require dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME']))) . DIRECTORY_SEPARATOR . 'include.php';
	
function getCle($db)
{	
	$cle = getNewCode();
	// Récupérer le User et ses clés
	$query = 'SELECT count(*) as cpt FROM '.cms_db_prefix().'module_oscs_user WHERE cni = ?';

	$result = $db->Execute($query,array($cle));
	if ($result === false){die("Database error durant la recherche de l'utilisateur!");}
	
	while ($row = $result->FetchRow())
	{
		if($row['cpt'] != 0)
		{
			return getCle();
		}
		break;
	}
	return $cle;

}

function getNewCode()
{	
	$cles = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$len = strlen($cles);
	$code = "";
	for($i = 0; $i < 50; $i++)
	{
		$code .= substr($cles, rand(0, $len-1),1);
	}
	return $code;
}

$db =& $gCms->GetDb();
$oscs =& $gCms->modules["OpenStatisticsCommunityServer"]['object'];

//Arrivé à ce niveau on enregistre les résultats
$query = 'INSERT INTO '.cms_db_prefix().'module_oscs_user ( id , cni, clepublic, cleprivee, date_creation, date_update) values (?,?,?,?,?,?)';
$sid = $db->GenID(cms_db_prefix().'module_oscs_user_seq');
$time = $oscs->_getTimeForDB($db);
$newCode = getCle($db);
$cles = getNewCode();

$param = array($sid, $newCode, $cles, null, $time, null);
$result = $db->Execute($query, $param);

if (!$result){die("Database error durant la livraison du CNI!");}

//echo strlen($newCode) ."|". strlen($cles);
echo "$newCode|$cles";

?>