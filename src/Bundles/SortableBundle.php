<?php

namespace Tatter\Workflows\Bundles;

use Tatter\Frontend\FrontendBundle;

class SortableBundle extends FrontendBundle
{
    protected function define(): void
    {
        $this
            ->addPath('sortable/Sortable.js')
            ->addPath('sortable/Sortable.min.js');
    }
}
