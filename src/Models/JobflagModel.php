<?php

namespace Tatter\Workflows\Models;

use CodeIgniter\Model;

/**
 * Job Flag Model.
 *
 * Flags are dynamic boolean extensions to the
 * `jobs` table in the format "name => timestamp".
 * They are accessed mostly through the Job entity.
 * There should only be one "name" per job at any
 * given time.
 */
class JobflagModel extends Model
{
	protected $table = 'jobflags';

	protected $primaryKey = 'id';

	protected $returnType = 'object';

	protected $useTimestamps = true;

	protected $updatedField = '';

	protected $useSoftDeletes = false;

	protected $allowedFields = ['job_id', 'name', 'created_at'];

	protected $validationRules = [
		'job_id' => 'required|is_natural_no_zero',
		'name'   => 'required',
	];
}
