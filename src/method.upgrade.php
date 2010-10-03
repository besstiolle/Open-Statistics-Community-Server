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
$oscs =& $gCms->modules["OpenStatisticsCommunityServer"]['object'];


if($oscs->GetVersion() <= '0.0.4')
{
	$db =& $gCms->GetDb();

	$taboptarray = array( 'mysql' => 'TYPE=MyISAM' );

	$dict = NewDataDictionary( $db );

	// table schema description
	$flds = "
		 texte X,
		 date_traduction " . CMS_ADODB_DT . "
	";
				
	//TODO : verifier les erreurs
	$sqlarray = $dict->CreateTableSQL( cms_db_prefix()."module_oscs_traduction",
					   $flds, 
					   $taboptarray);
	$dict->ExecuteSQLArray($sqlarray);
}

?>