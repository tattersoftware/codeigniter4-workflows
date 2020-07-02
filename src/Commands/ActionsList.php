<?php namespace Tatter\Workflows\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;
use Tatter\Workflows\Models\ActionModel;

class ActionsList extends BaseCommand
{
    protected $group       = 'Workflows';
    protected $name        = 'actions:list';
    protected $description = 'List all registered actions';
    
	protected $usage     = 'actions:list';
	protected $arguments = [];

	public function run(array $params = [])
    {
		$actions = new ActionModel();
		
		// get all actions
		$rows = $actions
			->select('id, name, category, uid, role, class, summary')
			->orderBy('name', 'asc')
			->get()->getResultArray();

		if (empty($rows))
		{
			CLI::write('There are no registered actions.', 'yellow');
		}
		else
		{
			$thead = ['Action ID', 'Name', 'Category', 'UID', 'Role', 'Class', 'Summary'];
			CLI::table($rows, $thead);
		}
	}
}
