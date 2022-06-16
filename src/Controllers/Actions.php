<?php

namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use Tatter\Workflows\Models\ActionModel;

class Actions extends Controller
{
    // Display a list of available actions
    public function index()
    {
        $data = [
            'layout'  => config('Layouts')->manage,
            'actions' => (new ActionModel())->orderBy('name')->findAll(),
        ];

        return view('Tatter\Workflows\Views\actions\index', $data);
    }
}
