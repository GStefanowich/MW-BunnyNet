<?php

namespace MediaWiki\Extension\Bunny\Hooks;

class CacheHooks implements
    \MediaWiki\Cache\Hook\HtmlCacheUpdaterAppendUrlsHook,
    \MediaWiki\Cache\Hook\HtmlCacheUpdaterVaryUrlsHook {

    /**
     * @inheritdoc
     */
    public function onHtmlCacheUpdaterAppendUrls( $title, $mode, &$append ): void {
        wfDebugLog('TheElm', json_encode($append));
    }

    /**
     * @inheritdoc
     */
    public function onHtmlCacheUpdaterVaryUrls( $urls, &$append ): void {
        wfDebugLog('TheElm', json_encode([
            $urls,
            $append,
        ]));
    }
}