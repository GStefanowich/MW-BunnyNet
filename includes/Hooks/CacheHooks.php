<?php

namespace MediaWiki\Extension\Bunny\Hooks;

use MediaWiki\Extension\Bunny\Bunny;

class CacheHooks implements
    \MediaWiki\Cache\Hook\HtmlCacheUpdaterAppendUrlsHook,
    \MediaWiki\Cache\Hook\HtmlCacheUpdaterVaryUrlsHook {

    public function __construct(
        private readonly Bunny $api
    ) {
    }

    /**
     * @inheritdoc
     */
    public function onHtmlCacheUpdaterAppendUrls( $title, $mode, &$append ): void {
        $append[] = 'tag://' . $this->api->getTitleCacheKey($title);
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