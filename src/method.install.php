<?php
#-------------------------------------------------------------------------
# Module: Open Statistics Community Server - Enregistreur de rapport de statistiques 
#		  pour Cms Made Simple. Pour toute information, consultez la page d'accueil 
#         du projet : http://www.cmsmadesimple.fr/rts-client.html
# Version: 0.0.1, Kevin Danezis Aka "Bess"
# Author can be join on the french forum : http://www.cmsmadesimple.fr/forum 
#        or by email : besstiolle [plop] gmail [plap] com
# Method: Install
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

$taboptarray = array( 'mysql' => 'TYPE=MyISAM' );

$dict = NewDataDictionary( $db );


/** TABLE CONTENANT LES USERS **/
$flds = "
     id I KEY,
	 cni C(50) ,
	 clepublic C(50) ,
	 cleprivee C(50) ,
	 date_creation " . CMS_ADODB_DT . " ,
	 date_update " . CMS_ADODB_DT . "
";
			
$sqlarray = $dict->CreateTableSQL( cms_db_prefix()."module_oscs_user",
				   $flds, 
				   $taboptarray);
$dict->ExecuteSQLArray($sqlarray);


$query = 'ALTER TABLE '.cms_db_prefix().'module_oscs_user ADD INDEX (date_creation)';
if ($db->Execute($query) === false)
{
	print_r($db);
	die('erreur grave durant l\'installation 1 ');
}
$query = 'ALTER TABLE '.cms_db_prefix().'module_oscs_user ADD INDEX (id)';
if ($db->Execute($query) === false)
{
	die('erreur grave durant l\'installation 2');
}

$query = 'ALTER TABLE '.cms_db_prefix().'module_oscs_user ADD INDEX (cni)';
if ($db->Execute($query) === false)
{
	die('erreur grave durant l\'installation 3');
}

$db->CreateSequence(cms_db_prefix()."module_oscs_user_seq");


/** TABLE CONTENANT LES RAPPORTS **/
$flds = "
     id I KEY,
	 user_id I ,
	 reponse C(10),
	 rapport X,
	 date_reception " . CMS_ADODB_DT . "
";
			
//TODO : vérifier les erreurs
$sqlarray = $dict->CreateTableSQL( cms_db_prefix()."module_oscs_rapport",
				   $flds, 
				   $taboptarray);
$dict->ExecuteSQLArray($sqlarray);


$query = 'ALTER TABLE '.cms_db_prefix().'module_oscs_rapport ADD INDEX (date_reception)';
if ($db->Execute($query) === false)
{
	die('erreur grave durant l\'installation 6');
}
$query = 'ALTER TABLE '.cms_db_prefix().'module_oscs_rapport ADD INDEX (user_id)';
if ($db->Execute($query) === false)
{
	die('erreur grave durant l\'installation 7');
}

$query = 'ALTER TABLE '.cms_db_prefix().'module_oscs_rapport ADD INDEX (reponse)';
if ($db->Execute($query) === false)
{
	die('erreur grave durant l\'installation 8');
}
// create a sequence
$db->CreateSequence(cms_db_prefix()."module_oscs_rapport_seq");

/** TABLE CONTENANT LES PARTIES DE RAPPORTS MULTIPLES **/
$flds = "
     id I KEY,
	 cni C(50) ,
	 resume C(32),
	 size I,
	 nbpart I,
	 data X
";
			
//TODO : vérifier les erreurs
$sqlarray = $dict->CreateTableSQL( cms_db_prefix()."module_oscs_rapport_tmp",
				   $flds, 
				   $taboptarray);
$dict->ExecuteSQLArray($sqlarray);


$query = 'ALTER TABLE '.cms_db_prefix().'module_oscs_rapport_tmp ADD INDEX (cni)';
if ($db->Execute($query) === false)
{
	die('erreur grave durant l\'installation 6');
}

// create a sequence
$db->CreateSequence(cms_db_prefix()."module_oscs_rapport_tmp_seq");





// create a permission
$this->CreatePermission('Set Open Statistics Community Server Prefs','OSC Server : Set Prefs');


// put mention into the admin log
$this->Audit( 0, 
	      $this->Lang('friendlyname'), 
	      $this->Lang('installed', $this->GetVersion()) );
?>