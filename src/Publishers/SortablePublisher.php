<?php

namespace Tatter\Workflows\Publishers;

use Tatter\Frontend\FrontendPublisher;

class SortablePublisher extends FrontendPublisher
{
    protected string $vendorPath = 'npm-asset/sortablejs';
    protected string $publicPath = 'sortable';

    public function publish(): bool
    {
        return $this
            ->addPath('Sortable.js')
            ->addPath('Sortable.min.js')
            ->merge(true);
    }
}
