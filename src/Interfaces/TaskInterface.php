<?php namespace Tatter\Workflows\Interfaces;

use Myth\Auth\Entities\User;

interface TaskInterface
{
	// handle anything that needs to happen before this task can run
	// NOTE: called during job progression *and* regression
	public function init();
	
	// run when job arrives while progressing through the workflow
	public function up();
	
	// run when job returns while regressing back through the workflow
	public function down();
	
	// display a view or form for user interaction
	public function ui();
	
	// process user/form input
	public function process($data);
	
	// handle anything that needs to happen before the task finishes
	public function finalize();
}
