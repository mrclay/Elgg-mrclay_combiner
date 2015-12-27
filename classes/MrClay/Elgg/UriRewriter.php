<?php

namespace MrClay\Elgg;

/**
 * Rewrite file-relative URIs as root-relative in CSS files
 */
class UriRewriter {

    private $prependPath = '';

    /**
     * In CSS content, prepend a path to relative URIs
     * 
     * @param string $css
     * 
     * @param string $path The path to prepend.
     * 
     * @return string
     */
    public function prepend($css, $path)
    {
        $this->prependPath = rtrim($path, '/') . '/';
        
        $css = $this->trimUrls($css);
        
        // append
        $css = preg_replace_callback('/@import\\s+([\'"])(.*?)[\'"]/'
            ,array($this, 'processUriCB'), $css);
        $css = preg_replace_callback('/url\\(\\s*([\'"](.*?)[\'"]|[^\\)\\s]+)\\s*\\)/'
            ,array($this, 'processUriCB'), $css);

        return $css;
    }

    /**
     * Remove instances of "./" and "../" where possible from a root-relative URI
     *
     * @param string $uri
     *
     * @return string
     */
    public function removeDots($uri)
    {
        $uri = str_replace('/./', '/', $uri);
        // inspired by patch from Oleg Cherniy
        do {
            $uri = preg_replace('@/[^/]+/\\.\\./@', '/', $uri, 1, $changed);
        } while ($changed);
        return $uri;
    }

    /**
     * @param string $css
     *
     * @return string
     */
    private function trimUrls($css)
    {
        return preg_replace('/
            url\\(      # url(
            \\s*
            ([^\\)]+?)  # 1 = URI (assuming does not contain ")")
            \\s*
            \\)         # )
        /x', 'url($1)', $css);
    }

    /**
     * @param array $m
     *
     * @return string
     */
    private function processUriCB($m)
    {
        // $m matched either '/@import\\s+([\'"])(.*?)[\'"]/' or '/url\\(\\s*([^\\)\\s]+)\\s*\\)/'
        $isImport = ($m[0][0] === '@');
        // determine URI and the quote character (if any)
        if ($isImport) {
            $quoteChar = $m[1];
            $uri = $m[2];
        } else {
            // $m[1] is either quoted or not
            $quoteChar = ($m[1][0] === "'" || $m[1][0] === '"')
                ? $m[1][0]
                : '';
            $uri = ($quoteChar === '')
                ? $m[1]
                : substr($m[1], 1, strlen($m[1]) - 2);
        }
        // if not root/scheme relative and not starts with scheme
        if (!preg_match('~^(/|[a-z]+\:)~', $uri)) {
            // URI is file-relative: rewrite depending on options
            $uri = $this->prependPath . $uri;
            if ($uri[0] === '/') {
                $root = '';
                $rootRelative = $uri;
                $uri = $root . $this->removeDots($rootRelative);
            } elseif (preg_match('@^((https?\:)?//([^/]+))/@', $uri, $m) && (false !== strpos($m[3], '.'))) {
                $root = $m[1];
                $rootRelative = substr($uri, strlen($root));
                $uri = $root . $this->removeDots($rootRelative);
            }
        }
        return $isImport
            ? "@import {$quoteChar}{$uri}{$quoteChar}"
            : "url({$quoteChar}{$uri}{$quoteChar})";
    }
}
