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
  {/literal}line1 = {$line1}{literal}
	plot1 = $.jqplot('chart1', [line1], {
	  title: 'R&eacute;partition des versions sur le march&eacute;',
	  seriesDefaults:{renderer:$.jqplot.PieRenderer, rendererOptions:{sliceMargin:8}}, 
	  legend:{show:true, escapeHtml:true}
	});
  });
  </script>
{/literal}
<div id="chart1" style="margin-top:20px; width:550px; height:500px;"></div>
{/strip}