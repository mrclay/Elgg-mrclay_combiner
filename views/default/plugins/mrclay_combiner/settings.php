<?php

namespace MrClay\CombinePlugin;

$suggestions = [
	'text',
	'elgg/Plugin',
	'elgg/init',
	'elgg/ready',
	'elgg/spinner',
	'elgg/reportedcontent',
	'core/river/filter',
	'forms/comment/save',
	'groups/navigation',
];
$suggestions = array_filter($suggestions, function ($module) {
	return elgg_view_exists("$module.js");
});

$suggestions = array_map(function ($module) {
	return elgg_view('output/url', [
		'href' => '#',
		'text' => $module,
		'data-suggested-module' => $module,
	]);
}, $suggestions);

$modules_textarea = elgg_view('input/plaintext', [
	'name' => 'params[modules]',
	'value' => elgg_get_plugin_setting('modules', 'mrclay_combiner', ''),
]);

?>
<div>
	<label>AMD Modules to load with elgg.js</label>
	<?= $modules_textarea ?>
	<p>Suggestions: <?= implode(', ', $suggestions); ?></p>
</div>

<?php
$site_lang = elgg_get_config('language');
$inline_site_lang = elgg_get_plugin_setting('inline_lang', 'mrclay_combiner', '');
if ($site_lang) {
	?>
<div>
	<?= elgg_view('input/checkbox', [
		'name' => 'params[inline_lang]',
		'checked' => (bool)$inline_site_lang,
		'label' => "Inline the " . elgg_view('output/url', [
				'href' => elgg_get_simplecache_url("languages/$site_lang.js"),
				'text' => 'site language module',
				'target' => '_blank',
			]),
	]) ?>
	<p class="elgg-text-help">If most users use a different language, this may not be wise.</p>
</div>
	<?php
}
?>

<h3>Select which resources to link in head</h3>
<?php

foreach (get_links_views() as $name => $view) {
	$img_attrs = [
		'alt' => '',
		'src' => elgg_get_simplecache_url($view),
		'style' => 'vertical-align:middle',
	];
	if (pathinfo($view, PATHINFO_EXTENSION) === 'svg') {
		$img_attrs['width'] = '128';
	}

	$input = elgg_view('input/checkbox', [
		'name' => "params[links-$name]",
		'checked' => (bool)elgg_get_plugin_setting("links-$name", 'mrclay_combiner', 'On'),
		'label' => "$name ($view) " . elgg_format_element('img', $img_attrs),

	]);
	echo "<div>$input</div>";
}

?>
<script>
require(['jquery'], function ($) {
	$(function () {
		var $ta = $('textarea[name="params[modules]"]');

		$(document).on('click', '[data-suggested-module]', function () {
			var module = $(this).data('suggestedModule');
			$ta.text($ta.text() + "\n" + module);
			return false;
		});
	});
});
</script>