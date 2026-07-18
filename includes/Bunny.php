<?php

namespace MediaWiki\Extension\Bunny;

use MediaWiki\Config\Config;
use MediaWiki\Http\HttpRequestFactory;
use MediaWiki\Json\FormatJson;
use MediaWiki\Page\ExistingPageRecord;
use MediaWiki\Title\Title;

class Bunny {
    public const SERVICE_NAME = 'BunnyNet';
    public const API_HOSTNAME = 'api.bunny.net';

    public readonly ?string $apiKey;
    public readonly ?string $zoneId;

    public function __construct(
        private readonly HttpRequestFactory $http,
        Config $config
    ) {
        $this->apiKey = $config->get('BunnyApiKey');
        $this->zoneId = null;
    }

    public function purgePageRecord( ExistingPageRecord $record, bool $strict = true ): bool {
        return $this->purgeTitle(Title::newFromPageReference($record), $strict);
    }

    public function purgeTitle( Title $title, bool $strict = true ): bool {
        return $this->purgeUrl($title->getFullURL(), $strict);
    }

    public function purgeUrl( string $url, bool $strict = true ): bool {
        wfDebugLog('TheElm', 'Purging URL ' . $url);
        return $this->authedHttpRequest('/purge', [
            'url' => $url,
            'async' => false,
            'exactPath' => $strict,
        ]);
    }

    public function purgeTag( Title|string $tag, ?string $zoneId = null ): bool {
        $zoneId ??= $this->zoneId;

        if ( !$zoneId ) {
            return false;
        }

        if ( $tag instanceof Title ) {
            $tag = $this->getTitleCacheKey($tag);
        }

        wfDebugLog('TheElm', 'Purging tag ' . $tag . ' in ' . $zoneId);
        return $this->authedHttpRequest('/pullzone/' . $zoneId . '/purgeCache', [
            'CacheTag' => $tag,
        ]);
    }

    private function authedHttpRequest( string $path, array $data ): bool {
        if ( !$this->apiKey ) {
            return false;
        }

        $request = $this->http->create(
            'https://' . self::API_HOSTNAME . $path,
            [
                'method' => 'POST',
                'userAgent' => 'MediaWiki/' . MW_VERSION,
                'postData' => FormatJson::encode($data),
            ]
        );

        // Set the header to avoid default fallback 'application/x-www-form-urlencoded'
        $request->setHeader('Content-Type', 'application/json');

        // Execute the request
        return $request->execute()
            ->isOK();
    }

    public function getTitleCacheKey( Title $title ): string {
        return base64_encode($title->getBaseTitle());
    }

    /**
     * Configure the image source
     * 
     * @param $metadata
     * @return void
     */
    public static function extensionRegister( $metadata ): void {
        global $wgEventRelayerConfig;

        $wgEventRelayerConfig ??= [];
        $wgEventRelayerConfig['cdn-url-purges'] = [
            'class' => \MediaWiki\Extension\Bunny\Events\Relayer::class,
        ];
    }
}