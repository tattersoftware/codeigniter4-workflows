<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use Tatter\Workflows\Models\ActionModel;

class Actions extends Controller
{
    // Display a list of available actions
    public function index()
    {
        $data = [
            'layout'  => config('Workflows')->layouts['manage'],
            'actions' => (new ActionModel())->orderBy('name')->findAll(),
        ];

        return view('Tatter\Workflows\Views\actions\index', $data);
    }
}
