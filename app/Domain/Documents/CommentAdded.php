<?php

namespace App\Domain\Documents;

use InertiaLive\Attributes\LiveTopic;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

#[LiveTopic('documents.{documentUuid}', props: ['comments'])]
final class CommentAdded extends ShouldBeStored
{
    public function __construct(
        public readonly string $documentUuid,
        public readonly string $body,
    ) {}
}
