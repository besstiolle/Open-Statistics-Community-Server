{literal}
  <style type="text/css" media="screen">
    .jqplot-axis {
      font-size: 1em;
    }
    .jqplot-legend,.jqplot-table-legend {
      font-size: 1.2em;
    }
	
	
  </style>
  <script type="text/javascript" language="javascript">
  $(document).ready(function(){
  
	$("table").tablesorter({widgets: ['zebra']}) 
  
  
	line1 = {/literal}{$line1}{literal}
	line2 = {/literal}{$line2}{literal}
	line3 = {/literal}{$line3}{literal}
	line4 = {/literal}{$line4}{literal}
	
	{/literal}{foreach from=$syntheseCmsModule item=entry}{literal}
		linem_{/literal}{$entry->name}{literal} = {/literal}{$entry->versionLine}{literal}
	{/literal}{/foreach}{literal}

	plot1 = $.jqplot('chart1', [line1], {
	  title: 'R&eacute;partition des versions sur le march&eacute;',
	  seriesDefaults:{renderer:$.jqplot.PieRenderer, rendererOptions:{sliceMargin:8}}, 
	  legend:{show:true, escapeHtml:true}
	});
	plot2 = $.jqplot('chart2', [line2], {
	  title: '&Ecirc;tat des versions PHP en services',
	  seriesDefaults:{renderer:$.jqplot.PieRenderer, rendererOptions:{sliceMargin:8}}, 
	  legend:{show:true, escapeHtml:true}
	});
	plot3 = $.jqplot('chart3', [line3], {
	  title: 'M&eacute;moire disponible sur les installations (64Mo est recommand&eacute;)',
	  seriesDefaults:{renderer:$.jqplot.PieRenderer, rendererOptions:{sliceMargin:8}}, 
	  legend:{show:true, escapeHtml:true}
	});
	plot4 = $.jqplot('chart4', [line4], {
	  title: '&Eacute;tat du SafeMode PHP (Off est recommand&eacute;)',
	  seriesDefaults:{renderer:$.jqplot.PieRenderer, rendererOptions:{sliceMargin:8}}, 
	  legend:{show:true, escapeHtml:true}
	});
	{/literal}{foreach from=$syntheseCmsModule item=entry}{literal}
		plotm_{/literal}{$entry->name}{literal} = $.jqplot('chartm_{/literal}{$entry->name}{literal}', [linem_{/literal}{$entry->name}{literal}], {
		  title: 'Versions du module {/literal}{$entry->name}{literal} install&eacute;es',
		  seriesDefaults:{renderer:$.jqplot.PieRenderer, rendererOptions:{sliceMargin:8}}, 
		  legend:{show:true, escapeHtml:true}
		});
		$("#more_{/literal}{$entry->name}{literal}").fancybox({'titlePosition': 'inside','transitionIn': 'none','transitionOut': 'none'});{/literal}{/foreach}{literal}
  });
  


  
  </script>
{/literal}
<br/>
<p>
Retrouvez &eacute;galement toutes les autres rapports de statistiques  :
<ul>
{foreach from=$listeMois item=mymois}
<li>Rapport du {$mymois->generatelink}</li>
{/foreach}
</ul>
</p>

<fieldset style="width:50%"><legend>Synth&egrave;se des versions de Cms install&eacute;es en {$mois}</legend>
		<table cellspacing="0" class="tablesorter">
			<thead>
				<tr>
					<th>{$module->Lang('th_version')}</th>
					<th>{$module->Lang('th_count')}</th>
					<th>{$module->Lang('th_percent')}</th>
				</tr>
			</thead>
			<tbody>
			{if count($syntheseCmsVersion) == 0}<tr><td colspan='3'>Aucun enregistrement</td></tr>{/if}
			{foreach from=$syntheseCmsVersion item=entry}
				<tr class="{$entry->rowclass}">
					<td>{$entry->name}</td>
					<td>{$entry->count}</td>
					<td>{$entry->percent}</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
</fieldset>

	
    <div id="chart1" style="margin-top:20px; width:550px; height:500px;"></div>

<fieldset style="width:95%;"><legend>Synth&egrave;se des modules install&eacute;s et activ&eacute;s en {$mois}</legend>
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
							<div title='aucune information disponible' style="background:#CCC;height:5px;width:100%;"></div>
						{else}
							<div title='{$entry->traductionRealisee}/{$entry->traductionTotale}' style="background:#900;height:5px;width:100%;">	
							<div title='{$entry->traductionRealisee}/{$entry->traductionTotale}' style="background:#070;height:5px;width:{$entry->traductionPourcent}%;"></div></div>
						{/if}
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
</fieldset>
	
	<div id="chart2" style="margin-top:20px; width:550px; height:500px;"></div>
	<div id="chart3" style="margin-top:20px; width:550px; height:500px;"></div>
	<div id="chart4" style="margin-top:20px; width:550px; height:500px;"></div>

<div style="height:0;left:-10000px;overflow:hidden;top:-10000px;width:0;">	
	{foreach from=$syntheseCmsModule item=entry}
	<div class='stat_panel' id='cadrem_{$entry->name}'>
		 <div id="chartm_{$entry->name}" style="margin-top:20px; width:550px; height:500px;"></div>
	</div>
	{/foreach}
</div>

{if $backlink != null}{$backlink}{$module->Lang('back')}{/if}

