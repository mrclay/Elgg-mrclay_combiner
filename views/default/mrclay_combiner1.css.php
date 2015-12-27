<?php

$view = 'font-awesome/css/font-awesome.css';

$rewriter = new \MrClay\Elgg\UriRewriter();
$url = elgg_get_simplecache_url($view);
$url = preg_replace('~^https?\\://[^/]+~', '', $url);
$css = elgg_view($view);
$css = $rewriter->prepend($css, dirname($url));

echo $css;
