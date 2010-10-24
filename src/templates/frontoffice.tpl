<fieldset id="historique"  style="width:60%;">
	<legend>Petit Historique</legend>
	il y a eu depuis le {$min_date_reception|cms_date_format}
	<br />	<span class='grand'>{$cptUser}</span> inscriptions au service 
	<br /> g&eacute;n&eacute;rant <span class='grand'>{$cptRapport}</span> rapports diff&eacute;rents.
</fieldset>

<br/>
<p>
Retrouvez &eacute;galement toutes les autres rapports de statistiques  :
<ul>
{foreach from=$listeMois item=mymois}
<li>Rapport : {$mymois->generatelink}</li>
{/foreach}
</ul>
<center>
	{$linkDefault} - {$linkListeModule} - {$linkTop}
</center>
</p>
<br/><br/>

