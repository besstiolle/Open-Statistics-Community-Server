<?php
class OpenStatisticsCommunityServer extends CMSModule
{

  function GetName()
  {
    return get_class($this);
  }

  function GetFriendlyName()
  {
    return $this->Lang('friendlyname');
  }

  function GetVersion()
  {
    return '0.0.4';
  }
  
  function GetHelp()
  {
    return $this->Lang('help');
  }
  
  function GetAuthor()
  {
    return 'Kevin Danezis (Bess)';
  }

  function GetAuthorEmail()
  {
    return 'besstiolle@gmail.com';
  }
  
  function GetChangeLog()
  {
    return $this->Lang('changelog');
  }
  
  /**
   * IsPluginModule()
   * @return bool True if this module can be included in page and or template
   */
  function IsPluginModule()
  {
    return true;
  }

  function HasAdmin()
  {
    return true;
  }

  function GetAdminSection()
  {
    return 'extensions';
  }

  function GetAdminDescription()
  {
    return $this->Lang('moddescription');
  }

  function VisibleToAdminUser()
  {
    return true;
  }
  
  function GetDependencies()
  {
    return array();
  }

  function MinimumCMSVersion()
  {
    return "1.6.7";
  }
  
  function SetParameters()
  {
	$this->RegisterModulePlugin();
	$this->RestrictUnknownParams();
	
	$this->CreateParameter('mois', 201009, 'todo');
	$this->SetParameterType('mois',CLEAN_INT);
	
	
	$this->CreateParameter('mod', null, 'todo');
	$this->SetParameterType('mod',CLEAN_STRING);
	
  }
  
  function InstallPostMessage()
  {
    return $this->Lang('postinstall',$this->GetVersion());
  }

  function UninstallPostMessage()
  {
    return $this->Lang('postuninstall');
  }
  
  function UninstallPreMessage()
  {
    return $this->Lang('really_uninstall');
  }
   
	function _dbToDate($stringDate)
	{
		return mktime(substr($stringDate, 11,2),
					substr($stringDate, 14,2),
					substr($stringDate, 17,2),
					substr($stringDate, 5,2),
					substr($stringDate, 8,2),
					substr($stringDate, 0,4));
	}
  
	function _getTimeForDB($db)
	{
		return trim($db->DBTimeStamp(time()), "'");
	}
	
	function _GenerationCle($Texte,$CleDEncryptage)
	{
	  $CleDEncryptage = md5($CleDEncryptage);
	  $Compteur=0;
	  $VariableTemp = "";
	  for ($Ctr=0;$Ctr<strlen($Texte);$Ctr++)
		{
		if ($Compteur==strlen($CleDEncryptage))
		  $Compteur=0;
		$VariableTemp.= substr($Texte,$Ctr,1) ^ substr($CleDEncryptage,$Compteur,1);
		$Compteur++;
		}
	  return $VariableTemp;
	}

	function _Decrypte($Texte,$Cle)
	{
	  $Texte = $this->_GenerationCle(base64_decode($Texte),$Cle);
	  $VariableTemp = "";
	  for ($Ctr=0;$Ctr<strlen($Texte);$Ctr++)
		{
		$md5 = substr($Texte,$Ctr,1);
		$Ctr++;
		$VariableTemp.= (substr($Texte,$Ctr,1) ^ $md5);
		}
	  return $VariableTemp;
	} 
  
}
?>
