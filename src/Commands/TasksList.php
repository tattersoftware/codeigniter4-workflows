<?php namespace Tatter\Workflows\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;
use Tatter\Workflows\Models\TaskModel;

class TasksList extends BaseCommand
{
    protected $group       = 'Workflows';
    protected $name        = 'tasks:list';
    protected $description = 'List all registered tasks';
    
	protected $usage     = 'tasks:list';
	protected $arguments = [ ];

	public function run(array $params = [])
    {
		$tasks = new TaskModel();
		
		// get all tasks
		$rows = $tasks
			->select('id, name, category, uid, class, summary')
			->orderBy('name', 'asc')
			->get()->getResultArray();

		if (empty($rows))
		{
			CLI::write( CLI::color('There are no registered tasks.', 'yellow') );
		}
		else
		{
			$thead = ['Task ID', 'Name', 'Category', 'UID', 'Class', 'Summary'];
			CLI::table($rows, $thead);
		}
	}
}
