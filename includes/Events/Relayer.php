<?php

namespace MediaWiki\Extension\Bunny\Events;

use MediaWiki\Extension\Bunny\Bunny;
use MediaWiki\MediaWikiServices;
use Wikimedia\EventRelayer\EventRelayer;
use Wikimedia\Parsoid\Utils\UrlUtils;

class Relayer extends EventRelayer {
    public function __construct( array $params ) {
        parent::__construct( $params );
    }

    protected function doNotify( $channel, array $events ): void {
        // Notify is only for URL purges
        if ( $channel !== 'cdn-url-purges' ) {
            return;
        }

        /** @var Bunny $cdn */
        $cdn = MediaWikiServices::getInstance()
            ->get(Bunny::SERVICE_NAME);

        foreach ( $events as $event ) {
            $url = $event['url'];
            $time = $event['timestamp'];

            $parsed = UrlUtils::parseUrl($url);
            $success = match($parsed['scheme']) {
                'http', 'https' => $cdn->purgeUrl($url),
                'tag' => $cdn->purgeTag($parsed['authority']),
                default => false,
            };

            if ( !$success ) {
                wfDebugLog('TheElm', 'Failed to purge ' . $url);
            }
        }
    }
}