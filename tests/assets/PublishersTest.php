<?php

namespace Tatter\Workflows\Publishers;

use Tatter\Frontend\Test\PublishersTestCase;

/**
 * @internal
 */
final class PublishersTest extends PublishersTestCase
{
    public function publisherProvider(): array
    {
        return [
            [
                SortablePublisher::class,
                [
                    'sortable/Sortable.js',
                    'sortable/Sortable.min.js',
                ],
            ],
        ];
    }
}
