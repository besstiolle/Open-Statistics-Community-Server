<?php
#-------------------------------------------------------------------------
# Module: Open Statistics Community Server - Enregistreur de rapport de statistiques 
#		  pour Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/rts-client.html
# Version: 0.0.1, Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : besstiolle [plop] gmail [plap] com
# Method: Upgrade
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