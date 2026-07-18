<?php

namespace MediaWiki\Extension\Bunny\Hooks;

use MediaWiki\Extension\Bunny\Bunny;
use MediaWiki\Output\Hook\OutputPageBeforeHTMLHook;

class HeaderHooks implements OutputPageBeforeHTMLHook {
    public function __construct(
        private readonly Bunny $api
    ) {
    }

    public function onOutputPageBeforeHTML( $out, &$text ): void {
        $request = $out->getRequest();
        $response = $request->response();

        // Add the requested page as a CDN Tag
        $response->header('CDN-Tag: ' . $this->api->getTitleCacheKey($out->getTitle()), true);
    }
}