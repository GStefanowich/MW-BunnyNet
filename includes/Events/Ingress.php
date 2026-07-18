<?php

namespace MediaWiki\Extension\Bunny\Events;

use MediaWiki\DomainEvent\DomainEventIngress;
use MediaWiki\Extension\Bunny\Bunny;

class Ingress extends DomainEventIngress implements
    \MediaWiki\Page\Event\PageCreatedListener,
    \MediaWiki\Page\Event\PageMovedListener,
    \MediaWiki\Page\Event\PageDeletedListener {

    public function __construct(
        private readonly Bunny $api
    ) {
    }

    public function handlePageCreatedEvent( \MediaWiki\Page\Event\PageCreatedEvent $event ): void {
        $this->api->purgePageRecord($event->getPageRecordAfter());
    }

    public function handlePageDeletedEvent( \MediaWiki\Page\Event\PageDeletedEvent $event ): void {
        $this->api->purgePageRecord($event->getPageRecordBefore());
    }

    public function handlePageMovedEvent( \MediaWiki\Page\Event\PageMovedEvent $event ): void {
        $this->api->purgePageRecord($event->getPageRecordBefore());
        $this->api->purgePageRecord($event->getPageRecordAfter());
    }
}