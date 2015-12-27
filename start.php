<?php

namespace MrClay\CombinePlugin;

elgg_register_event_handler('init', 'system', __NAMESPACE__ . '\\init');

function init() {
	merge_js();
	merge_css();

	elgg_register_plugin_hook_handler('head', 'page',
		__NAMESPACE__ . '\\filter_head_links');
	elgg_register_plugin_hook_handler('action', 'plugins/settings/save',
		__NAMESPACE__ . '\\filter_settings_save');
}

function merge_js() {
	// save 6 JS requests

	elgg_unregister_js('jquery');
	elgg_extend_view('elgg.js', 'jquery.js', 1);
	elgg_extend_view('elgg.js', 'separator.js', 1);

	elgg_unregister_js('jquery-ui');
	elgg_extend_view('elgg.js', 'jquery-ui.js', 1);
	elgg_extend_view('elgg.js', 'separator.js', 1);

	elgg_unregister_js('elgg.require_config');
	elgg_extend_view('elgg.js', 'elgg/require_config.js', 1);
	elgg_extend_view('elgg.js', 'separator.js', 1);

	elgg_unregister_js('require');
	elgg_extend_view('elgg.js', 'require.js', 1);
	elgg_extend_view('elgg.js', 'separator.js', 1);

	elgg_extend_view('elgg.js', 'separator.js');
	elgg_unregister_js('lightbox');
	elgg_extend_view('elgg.js', 'lightbox.js');

	elgg_extend_view('elgg.js', 'separator.js');
	elgg_unregister_js('elgg.ui.river');
	elgg_extend_view('elgg.js', 'elgg/ui.river.js');

	elgg_extend_view('elgg.js', 'separator.js');
	elgg_extend_view('elgg.js', 'mrclay_combiner.js');
}

function merge_css() {
	// save 2 CSS requests

	elgg_extend_view('elgg.css', 'mrclay_combiner1.css', 400);
	elgg_extend_view('admin.css', 'mrclay_combiner1.css', 400);
	elgg_unregister_css('font-awesome');

	elgg_extend_view('elgg.css', 'mrclay_combiner2.css');
	elgg_extend_view('admin.css', 'mrclay_combiner2.css');
	elgg_unregister_css('lightbox');
}

function module_is_blacklisted($module) {
	if (preg_match('~^(elgg|jquery(-ui)?)$~', $module)) {
		return true;
	}
	if (preg_match('~^(languages/|text!)~', $module)) {
		return true;
	}
	return false;
}

function filter_head_links($h, $t, $v, $p) {
	foreach (get_links_views() as $name => $view) {
		if (!elgg_get_plugin_setting("links-$name", 'mrclay_combiner', 'On')) {
			unset($v['links'][$name]);
		}
	}

	return $v;
}

function get_links_views() {
	return [
		'apple-touch-icon' => 'favicon-128.png',
		'icon-ico' => 'favicon.ico',
		'icon-vector' => 'favicon.svg',
		'icon-16' => 'favicon-16.png',
		'icon-32' => 'favicon-32.png',
		'icon-64' => 'favicon-64.png',
		'icon-128' => 'favicon-128.png',
	];
}

function filter_settings_save($h, $t, $v, $p) {
	$params = get_input('params');
	$modules = $params['modules'];

	$modules = preg_split('~\s+~', $modules, -1, PREG_SPLIT_NO_EMPTY);
	$modules = array_unique($modules);

	foreach ($modules as $i => $module) {
		if (module_is_blacklisted($module)) {
			unset($modules[$i]);
			register_error("The module '$module' cannot be combined. It has been removed from the list.");
		}
	}

	$params['modules'] = implode("\n", $modules);
	set_input('params', $params);
}
