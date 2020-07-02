<?php namespace Tatter\Workflows\Interfaces;

interface ActionInterface
{		
	// Magic wrapper for getting values from the definition
	// (provided by ActionsTrait)
    public function __get(string $name);
	
	// Create the database record of this action based on its definition
	// (provided by ActionsTrait)
	public function register();
	
	// Soft delete this action from the database
	// (provided by ActionsTrait)
	public function remove();
	
	// Run when a job progresses forward through the workflow
	public function up();
	
	// Run when job regresses back through the workflow
	public function down();
}
