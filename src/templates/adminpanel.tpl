{$tabs_start}
   {* onglet général : listing des derniers rapport reçus des envois *}
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
   {* Fin onglet général *}
   
   {* onglet traduction *}
      {$traductionTpl}
	  {$area}
   {$tab_end}
   {* Fin onglet traduction *}
   
   {* onglet général : historique des envois *}
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
   {* Fin onglet général *}
{$tabs_end}

