<?php namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity;

class Action extends Entity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
