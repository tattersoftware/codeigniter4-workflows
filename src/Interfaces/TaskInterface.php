<?php namespace Tatter\Workflows\Interfaces;

interface TaskInterface
{		
	// Magic wrapper for getting values from the definition
	// (provided by TasksTrait)
    public function __get(string $name);
	
	// Create the database record of this task based on its definition
	// (provided by TasksTrait)
	public function register();
	
	// Soft delete this task from the database
	// (provided by TasksTrait)
	public function remove();
	
	// Run when a job progresses forward through the workflow
	public function up();
	
	// Run when job regresses back through the workflow
	public function down();
}
