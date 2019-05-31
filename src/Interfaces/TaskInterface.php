<?php namespace Tatter\Workflows\Interfaces;

use Myth\Auth\Entities\User;

interface TaskInterface
{	
	// provided by TasksTrait
	public function __construct();
	
	// magic wrapper for getting values from the definition
	// provided by TasksTrait
    public function __get(string $name);
	
	// create the database record of this task based on its definition
	// provided by TasksTrait
	public function register();
	
	// soft delete this task from the database
	// provided by TasksTrait
	public function remove();
	
	// run when a job progresses forward through the workflow
	public function up();
	
	// run when job regresses back through the workflow
	public function down();
}
