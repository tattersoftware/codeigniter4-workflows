<?php

namespace Tatter\Workflows\Bundles;

use Tatter\Assets\Test\BundlesTestCase;

/**
 * @internal
 */
final class BundlesTest extends BundlesTestCase
{
    public function bundleProvider(): array
    {
        return [
            [
                SortableBundle::class,
                [],
                [
                    'Sortable.js',
                    'Sortable.min.js',
                ],
            ],
            [
                WorkflowsBundle::class,
                [
                    'all.min.css',
                    'bootstrap.min.css',
                    'jquery.min.js', // Note that unlike most JS files this goes in <head>
                ],
                [
                    'bootstrap.bundle.min.js',
                    'Sortable.min.js',
                ],
            ],
        ];
    }
}
