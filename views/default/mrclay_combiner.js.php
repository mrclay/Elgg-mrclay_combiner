<?php

$modules = elgg_get_plugin_setting('modules', 'mrclay_combiner', '');
$modules = preg_split('~\s+~', $modules, -1, PREG_SPLIT_NO_EMPTY);

$filter = new \MrClay\AmdViewFilter();

$inline_lang = elgg_get_plugin_setting('inline_lang', 'mrclay_combiner', '');
$site_lang = elgg_get_config('language');
if ($inline_lang && $site_lang) {
	$content = elgg_view('languages.js', [
		'language' => $site_lang,
	]);
	$content = $filter->filter("languages/$site_lang.js", $content);
	echo $content . ";\n";
}

foreach ($modules as $module) {
	if (!elgg_view_exists("$module.js")) {
		continue;
	}

	$content = elgg_view("$module.js");
	$content = $filter->filter("$module.js", $content);
	echo $content . ";\n";
}
