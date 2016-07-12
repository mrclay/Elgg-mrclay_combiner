# HTTP Combiner for Elgg

This plugin reduces HTTP requests in the initial page load by merging files and AMD modules into `elgg.js`/`elgg.css`.

* Merges 6 external scripts into `elgg.js`
* Merges 2 CSS files into `elgg.css`
* Merges AMD modules of your choice into `elgg.js` (suggests 6 by default)
* Can also merge the site default language module
* Allows unregistering particular site icons

## Installation

```bash
cd path/to/elgg/mod
git clone git@github.com:mrclay/Elgg-mrclay_combiner.git mrclay_combiner
```
