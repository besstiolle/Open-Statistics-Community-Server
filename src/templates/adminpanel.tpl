{$tabs_start}
   {* onglet general : listing des derniers rapport recus des envois *}
      {$contenuTpl}
	  	<table cellspacing="0" class="pagetable">
			<thead>
				<tr>
					<th>{$module->Lang('cnitext')}</th>
					<th>{$module->Lang('datetext')}</th>
					<th>{$module->Lang('responsetext')}</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			{if count($listeContenu) == 0}<tr><td colspan='3'>Aucun enregistrement</td></tr>{/if}
			{foreach from=$listeContenu item=entry}
				<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
					<td>{$entry->cni}</td>
					<td>{$entry->date_reception|cms_date_format}</td>
					<td>{$entry->reponse}</td>
					<td>{$entry->showlink}</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
   {$tab_end}
   {* Fin onglet general *}
   
   {* onglet traduction *}
      {$traductionTpl}
	  {$area}
   {$tab_end}
   {* Fin onglet traduction *}
   
   {* onglet general : historique des envois *}
      {$generationTpl}
		<table cellspacing="0" class="pagetable">
			<thead>
				<tr>					
					<th>{$module->Lang('mois')}</th>
					<th>{$module->Lang('generer')}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$listeMois item=entry}
					<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
						<td>{$entry->mois}</td>
						<td>{$entry->generatelink}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
   {$tab_end}
   
   {$tpl_default}
	  <h3>{$module->Lang('info_template_default')}</h3>
	  {$listeGabaritdefault}
   {$tab_end}
   {$tpl_frontoffice}
	  <h3>{$module->Lang('info_template_frontoffice')}</h3>
	  {$listeGabaritfrontoffice}
   {$tab_end}
   {$tpl_showListeModule}
	  <h3>{$module->Lang('info_template_showListeModule')}</h3>
	  {$listeGabaritshowListeModule}
   {$tab_end}
   {$tpl_showTop}
	  <h3>{$module->Lang('info_template_showTop')}</h3>
	  {$listeGabaritshowTop}
   {$tab_end}
   {$tpl_vueDetail}
	  <h3>{$module->Lang('info_template_vueDetail')}</h3>
	  {$listeGabaritvueDetail}
   {$tab_end}
   
{$tabs_end}

