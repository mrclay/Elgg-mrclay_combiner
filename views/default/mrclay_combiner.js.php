<?php

$modules = elgg_get_plugin_setting('modules', 'mrclay_combiner', '');
$modules = preg_split('~\s+~', $modules, -1, PREG_SPLIT_NO_EMPTY);

$filter = new \MrClay\AmdViewFilter();

foreach ($modules as $module) {
	if (!elgg_view_exists("$module.js")) {
		continue;
	}

	$content = elgg_view("$module.js");
	$content = $filter->filter("$module.js", $content);
	echo $content . ";\n";
}
