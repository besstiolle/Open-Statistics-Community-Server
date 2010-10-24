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
	
		linem_{/literal}{$myModule->name}{literal} = {/literal}{$myModule->versionLine}{literal}

		plotm_{/literal}{$myModule->name}{literal} = $.jqplot('chartm_{/literal}{$myModule->name}{literal}', [linem_{/literal}{$myModule->name}{literal}], {
		  title: 'Versions du module {/literal}{$myModule->name}{literal} install&eacute;es',
		  seriesDefaults:{renderer:$.jqplot.PieRenderer, rendererOptions:{sliceMargin:8}}, 
		  legend:{show:true, escapeHtml:true}
		});
	
		{/literal}{*$("#more_{/literal}{$myModule->name}{literal}").fancybox({'titlePosition': 'inside','transitionIn': 'none','transitionOut': 'none'}); *}{literal}
		
		{/literal}{if $myModule->traductionTotale != 0}{literal}
		line1 = [[0,0],{/literal}{$myModule->traduction->line1}{literal}];
		line2 = [[0,0],{/literal}{$myModule->traduction->line2}{literal}];
		plotTrad = $.jqplot('chartTrad', [line1, line2], {
			stackSeries: true,
			legend: {show: true, location: 'nw'},
			title: 'Taux de traduction du module au cours du temps',
			seriesDefaults: {fill:true, showMarker: false},
			series: [
			{label: 'El&eacute;ments traduits'}, 
			{label: 'El&eacute;ments non traduits'}
			],
			axes: {
			  xaxis: {
				  ticks:[0,1,2,3,4,5,6], 
				  tickOptions:{formatString:'%d'}
			  }, 
			  yaxis: {min: 0, max: {/literal}{$myModule->traductionTotale}{literal}, numberTicks:5}
			}
		});
		{/literal}{/if}{literal}
  });
  
  </script>

{/literal}

	<div class='stat_panel' id='cadrem_{$myModule->name}'>
		
		<h2>Versions du module {$myModule->name} en {$mois}</h2> 
		
		<div id="chartm_{$myModule->name}" style="margin-top:20px; width:550px; height:500px;"></div>
		 
		<h2>T&eacute;l&eacute;charger ce module</h2> 
		<p>Le plus souvent, le module est disponible sur la forge de Cms Made Simple. Pour aller le t&eacute;l&eacute;charger il vous suffit de cliquer sur ce <a title='chercher le projet sur la forge' href='http://dev.cmsmadesimple.org/projects/{$myModule->name}'>lien</a>. Si une erreur apparait c'est que l'auteur du module a choisit de ne pas le partager publiquement !</p>
		
		{if $myModule->traductionTotale != 0}
		<h2>Traduction du module</h2> 
		 <div class="jqPlot" id="chartTrad" style="height:150px; width:550px;"></div>
		 <p><b>Aider &agrave; la traduction</b></p>
			<p>Vous pouvez aider &agrave; traduire le module de l'anglais au fran&ccedil;ais tr&egrave;s simplement et nous aider &agrave; am&eacute;liorer Cms Made Simple en le rendant plus ouvert au public. </p>
			<p>Pour ce faire c'est tr&egrave;s simple. Commencez par lire <a href='http://www.cmsmadesimple.fr/boutique-cms/documentation-gestion-de-contenu'>ce guide du traducteur</a>, et rendez vous sur le <a href='http://translations.cmsmadesimple.org/login.php'>Centre de traduction</a> afin de commencer le travail.</p>
			<p>La premi&egrave;re utilisation du service vous prendra 10 minutes pour tout configurer. Les utilisations suivantes sont instantan&eacute;es.</p>
			<p>r&eacute;capitulatif des url &agrave; retenir pour traduire ce module : </p>
			<ul>
				<li><a target='_blank' href='http://www.cmsmadesimple.fr/boutique-cms/documentation-gestion-de-contenu'>Le guide du traducteur</a></li>
				<li><a target='_blank' href='http://translations.cmsmadesimple.org/login.php'>Le centre de traduction</a></li>
				<li><a target='_blank' href='http://svn.cmsmadesimple.org/svn/translatecenter/modules/{$myModule->name}/lang/ext/fr_FR.php'>La derni&egrave;re version du fichier de langue Fr pour ce module</a> (si existant dans le Centre de traduction)</li>
			</ul>
		{/if}
		
		 <h2>Documentation du module</h2>
		 <p>Le WIKI de Cms Made Simple est accessible &agrave; tout le monde en lecture seule. La page d'aide du module {$myModule->name} est accessible sur ce <a href='http://wiki.cmsmadesimple.org/index.php/User_Handbook/Modules/{$myModule->name}/fr'>lien</a></p>
		 <p>Le WIKI de Cms Made Simple est accessible &eacute;galement en modification &agrave; toutes les personnes inscrites sur le <a href='http://forum.cmsmadesimple.org/'>forum <b>anglais</b></a>. Sit&ocirc;t votre login et mot de passe en poche, vous pourrez commencer &agrave; enrichir les pages.</p>
		 
		 <h2>Notation du module</h2>
		  *&agrave; venir :)*
		 
	</div>
	
	<p>{if $backlink != null}{$backlink}{/if}</p>


