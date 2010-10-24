{strip}
{literal}
  <script type="text/javascript" language="javascript">
  $(document).ready(function(){
	$("table").tablesorter({widgets: ['zebra']}); 
  });
  </script>
{/literal}
<fieldset style="width:95%;"><legend>Synth&egrave;se des modules install&eacute;s en {$mois}</legend>
		<table cellspacing="0" class="tablesorter">
			<thead>
				<tr>
					<th>{$module->Lang('th_module')}</th>
					<th>{$module->Lang('th_count')}</th>
					<th>{$module->Lang('th_percent')}</th>
					<th>{$module->Lang('th_traduction')}</th>
				</tr>
			</thead>
			<tbody>
			{if count($syntheseCmsModule) == 0}<tr><td colspan='3'>Aucun enregistrement</td></tr>{/if}
			{foreach from=$syntheseCmsModule item=entry}
				<tr class="{$entry->rowclass}">
					<td>{$entry->detailLien}</td>
					<td>{$entry->count}</td>
					<td>{$entry->percent}</td>
					<td>
						{if $entry->traductionTotale == 0}
							<span style='display:none;'>0</span>
							<div title="aucune information sur la traduction n'est disponible" style="background:#CCC;height:5px;width:100%;"></div>
						{else}
							<span style='display:none;'>{$entry->traductionPourcent}</span>
							<div title='{$entry->traductionRealisee}/{$entry->traductionTotale}' style="background:#900;height:5px;width:100%;">	
							<div title='{$entry->traductionRealisee}/{$entry->traductionTotale}' style="background:#070;height:5px;width:{$entry->traductionPourcent}%;"></div></div>
						{/if}
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
</fieldset>
{/strip}