<?php

use MediaWiki\Extension\Bunny\Bunny;
use MediaWiki\MediaWikiServices;

return [
    Bunny::SERVICE_NAME => static function(
        MediaWikiServices $services
    ): Bunny {
        return new Bunny(
            $services->getHttpRequestFactory(),
            $services->getMainConfig()
        );
    }
];