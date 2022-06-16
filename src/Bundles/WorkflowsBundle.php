<?php

namespace Tatter\Workflows\Bundles;

use Tatter\Assets\Bundle;
use Tatter\Frontend\Bundles\BootstrapBundle;
use Tatter\Frontend\Bundles\FontAwesomeBundle;
use Tatter\Frontend\Bundles\JQueryBundle;

class WorkflowsBundle extends Bundle
{
    /**
     * Applies any initial inputs after the constructor.
     */
    protected function define(): void
    {
        $this
            ->merge(new BootstrapBundle())
            ->merge(new FontAwesomeBundle())
            ->merge(new JQueryBundle())
            ->merge(new SortableBundle());
    }
}
