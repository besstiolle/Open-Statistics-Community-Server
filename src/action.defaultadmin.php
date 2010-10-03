<?php
#-------------------------------------------------------------------------
# Module: Open Statistics Community Server - Enregistreur de rapport de statistiques 
#		  pour Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/rts-client.html
# Version: 0.0.1, Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : besstiolle [plop] gmail [plap] com
# Method: action.defaultadmin
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

if (FALSE == empty($params['active_tab']))
{
	$tab = $params['active_tab'];
} else 
{
	$tab = '';
}

//On ajoute l'onglet Contenu + Autres
$tab_header = $this->StartTabHeaders();
$tab_header.= $this->SetTabHeader('contenu',$this->Lang('title_contenu'),('contenu' == $tab)?true:false);
$tab_header.= $this->SetTabHeader('traduction',$this->Lang('title_traduction'),('traduction' == $tab)?true:false);
$tab_header.= $this->SetTabHeader('generation',$this->Lang('title_generation'),('generation' == $tab)?true:false);
$tab_header.= $this->EndTabHeaders();

$this->smarty->assign('tabs_start',$tab_header.$this->StartTabContent());
$this->smarty->assign('tab_end',$this->EndTab());

//Contenu de l'onglet contenu
$this->smarty->assign('contenuTpl',$this->StartTab('contenu', $params));
include_once(dirname(__FILE__).'/function.admin_contenutab.php');

//Contenu de l'onglet traduction
$this->smarty->assign('traductionTpl',$this->StartTab('traduction', $params));
include_once(dirname(__FILE__).'/function.admin_areatraduction.php');

//Contenu de l'onglet generation
$this->smarty->assign('generationTpl',$this->StartTab('generation', $params));
include_once(dirname(__FILE__).'/function.admin_generatetab.php');


$this->smarty->assign('tabs_end',$this->EndTabContent());

// pass a reference to the module, so smarty has access to module methods
$smarty->assign_by_ref('module',$this);

echo $this->ProcessTemplate('adminpanel.tpl');
?>