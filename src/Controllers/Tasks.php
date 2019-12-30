<?php namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use Tatter\Workflows\Models\TaskModel;

class Tasks extends Controller
{
	// Display a list of available tasks
	public function index()
	{
		$data = [
			'layout' => config('Workflows')->layouts['manage'],
			'tasks'  => (new TaskModel())->orderBy('name')->findAll(),
		];
		
		return view('Tatter\Workflows\Views\tasks\index', $data);
	}
}
