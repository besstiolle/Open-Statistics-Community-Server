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

// Verification de la permission
if (! $this->CheckPermission('Set Open Statistics Community Server Prefs')) 
  return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));

    
$listeLigneTmp = explode('<br />',nl2br($params['area']));
$listeLigne = array();
$tmp = array();
foreach($listeLigneTmp as $ligne)
{
	if(substr($ligne, 0, 8) == "\r\n    * " && $ligne != "\r\n    * --------")
	{

		$ligne = str_replace("\r\n    * ", "",$ligne);
		$ligne = str_replace("Complete)", "",$ligne);
		$ligne = str_replace(" ", "",$ligne);
		list($name, $complement) =  explode("(", $ligne);
		list($done, $total) =  explode("/", $complement);
		
		$module = new stdclass();
		$module->name = $name;
		$module->done = $done;
		$module->total = $total;
		$listeLigne[$module->name] = $module;
	} 
}

$queryInsert = 'INSERT INTO '.cms_db_prefix().'module_oscs_traduction (texte,date_traduction) values (?,?)';
$param = array(serialize($listeLigne), $this->_getTimeForDB($db));
$result = $db->Execute($queryInsert, $param);
if ($result === false){die("Database error durant l'insert de la donn&eacute;e!");}
// redirect back to default admin page
$this->Redirect($id, 'defaultadmin', $returnid, array('tab_message'=> 'traductionupdated', 'active_tab' => 'traduction'));

?>