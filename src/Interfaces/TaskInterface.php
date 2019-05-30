<?php namespace Tatter\Workflows\Interfaces;

use Myth\Auth\Entities\User;

interface TaskInterface
{	
	// run when a job progresses forward through the workflow
	public function up();
	
	// run when job regresses back through the workflow
	public function down();
}
