<?php

namespace MrClay\CombinePlugin;

$suggestions = [
	'elgg/Plugin',
	'elgg/init',
	'elgg/ready',
	'elgg/spinner',
	'text',
];
if (elgg_is_active_plugin('reportedcontent')) {
	$suggestions[] = 'elgg/reportedcontent';
}

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