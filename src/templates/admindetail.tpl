
<fieldset style="width:50%"><legend>{$titre} {$date_reception|cms_date_format}</legend>
<ul>
<li>{$text_rapport->cms_version}</li>
<li>{$text_rapport->installed_modules|print_r}</li>
<li>{$text_rapport->config_info|print_r}</li>
<li>{$text_rapport->php_information|print_r}</li>
<li>{$text_rapport->server_info|print_r}</li>
<li>{$text_rapport->permission_info|print_r}</li>
</ul>
</fieldset>
{$backlink}{$module->Lang('back')}

