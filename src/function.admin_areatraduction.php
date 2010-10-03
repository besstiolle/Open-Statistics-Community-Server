<?php
if (!isset($gCms)) exit;

// Vérification de la permission
if (! $this->CheckPermission('Set Open Statistics Community Server Prefs')) 
  return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));


  
$startForm = $this->CreateFormStart($id, 'admin_saveTraduction', $returnid);
$endForm = $this->CreateFormEnd();
$area = $this->CreateTextArea(false,$id,'','area');
$submit = $this->CreateInputSubmit($id, 'areasubmit', $value='enregistrer');

$smarty->assign('area',$startForm.$area.'<br/>'.$submit.$endForm);

?>