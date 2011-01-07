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


  
// Get the historique
$query =  'SELECT r.id, u.clepublic, r.date_reception, r.rapport FROM '.cms_db_prefix().'module_oscs_rapport r,  '.cms_db_prefix().'module_oscs_user u ';
$query .= ' WHERE r.id = '.$params['rapportid'].' AND u.id = r.user_id';
$result = $db->Execute($query);


if ($result === false)
{
	echo "Database error durant la r&eacute;cup&eacute;ration des rapports!";
	exit;
}
$admintheme =& $gCms->variables['admintheme'];
while ($row = $result->FetchRow())
{
	$rapport = new stdClass;
	$rapport->id = $row['id'];
	$rapport->date_reception = $db->UnixTimeStamp($row['date_reception']);
	$rapport->clepublic = $row['clepublic'];
	$rapport->rapport = $row['rapport'];
	
	
	$arrayRapport = unserialize($this->_Decrypte($rapport->rapport, $rapport->clepublic));
	
	$text_rapport = new stdClass;
	if(isset($arrayRapport['cms_version']))
		$text_rapport->cms_version = $arrayRapport['cms_version'];
	if(isset($arrayRapport['installed_modules']))
		$text_rapport->installed_modules = $arrayRapport['installed_modules'];
	if(isset($arrayRapport['config_info']))
		$text_rapport->config_info = $arrayRapport['config_info'];
	if(isset($arrayRapport['php_information']))
		$text_rapport->php_information = $arrayRapport['php_information'];
	if(isset($arrayRapport['server_info']))
		$text_rapport->server_info = $arrayRapport['server_info'];
	if(isset($arrayRapport['permission_info']))
		$text_rapport->permission_info = $arrayRapport['permission_info'];
}

$backlink = $this->CreateLink($id, 'defaultadmin', $returnid, $admintheme->DisplayImage('icons/system/back.gif', $this->Lang('back'),'','','systemicon'));

$this->smarty->assign('text_rapport',$text_rapport);
$this->smarty->assign('titre',sprintf($this->Lang('detailtitre'),$rapport->id));
$this->smarty->assign('date_reception',$rapport->date_reception);
$this->smarty->assign('backlink',$backlink);
	  
$smarty->assign_by_ref('module',$this);

echo $this->ProcessTemplate('admindetail.tpl');
?>