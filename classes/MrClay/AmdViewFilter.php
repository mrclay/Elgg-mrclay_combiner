<?php
namespace MrClay;

/**
 * This filter adds AMD names to anonymous AMD modules defined in views.
 * 
 * @access private
 */
class AmdViewFilter {
	/**
	 * Given the view name, returns the AMD name.
	 * 
	 * @param string $name The name of the view (e.g., 'elgg/module.js')
	 * 
	 * @return string The AMD name (e.g., 'elgg/module'), or blank for no AMD name.
	 */
	private function getAmdName($name) {
		if (preg_match('~^(js/)?(.*)\\.js\\z~', $name, $m)) {
			return $m[2];
		}

		$pieces = explode("/", $name); // [js, elgg, module.js]
		if (count($pieces) <= 1 || $pieces[0] != 'js') {
			return '';
		}
	
		array_shift($pieces); // [elgg, module.js]
		$basename = basename(array_pop($pieces), ".js"); // module
		array_push($pieces, $basename); // [elgg, module]
		
		return implode("/", $pieces); // elgg/module
	}
	
	/**
	 * Inserts the AMD name into `$content` and returns the new value.
	 * 
	 * @param string $viewName The name of the view.
	 * @param string $content  The output of the view to be filtered.
	 * 
	 * @return string The new content with the AMD name inserted, if applicable.
	 */
	public function filter($viewName, $content) {
		$amdName = $this->getAmdName($viewName);
		
		if (!empty($amdName)) {
			$content = preg_replace('/^(\s*)define\(([^\'"])/m', "\${1}define(\"$amdName\", \$2", $content, 1);
		}
		
		return $content;
	}
}
