<?php

namespace Tatter\Workflows\Controllers;

use Tatter\Workflows\Models\ActionModel;

class Actions extends BaseController
{
    // Display a list of available actions
    public function index()
    {
        $data = [
            'layout'  => config('Layouts')->manage,
            'actions' => model(ActionModel::class)->orderBy('name')->findAll(),
        ];

        return view('Tatter\Workflows\Views\actions\index', $data);
    }
}
