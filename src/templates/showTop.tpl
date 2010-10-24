{strip}
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
  
	$("table").tablesorter({widgets: ['zebra']}); {/literal}
	
	line1 = {$line1}
	line2 = {$line2}
	line3 = {$line3}
	line4 = {$line4}
	
	{literal}
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
  });
  
  </script>
{/literal}
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
	<div id="chart2" style="margin-top:20px; width:550px; height:500px;"></div>
	<div id="chart3" style="margin-top:20px; width:550px; height:500px;"></div>
	<div id="chart4" style="margin-top:20px; width:550px; height:500px;"></div>


{/strip}