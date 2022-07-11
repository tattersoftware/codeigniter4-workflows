<?php

namespace Tatter\Workflows\Controllers;

use Tatter\Workflows\Factories\ActionFactory;

class Actions extends BaseController
{
    /**
     * Displays a list of available actions
     */
    public function index(): string
    {
        return view('Tatter\Workflows\Views\actions\index', [
            'layout'  => config('Layouts')->manage,
            'actions' => ActionFactory::getAllAttributes(),
        ]);
    }
}
