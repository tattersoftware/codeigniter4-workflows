<?= view($config->views['header'], ['current' => 'workflows']) ?>

<?php
if (empty($workflow)):
	echo '<p>Unable to locate that workflow!</p>';
	return;
endif;
?>
	
	<h2>Workflow</h2>

<?php
// check for messages
if (session()->has('success')):
?>
	<ul class="alert alert-success">
		<li><?= session('success'); ?></li>
	</ul>
<?php
endif;
?>
	<h3 class="mt-3">Details</h3>
	<div class="row mt-4">
		<div class="col-xl-4">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title"><i class="<?= $workflow->icon ?: 'far fa-circle' ?>"></i> <?= $workflow->name ?></h5>
					<h6 class="card-subtitle mb-2 text-muted"><?= $workflow->category ?: 'No' ?> Category</h6>
					<p class="card-text"><?= $workflow->summary ?></p>
				</div>
			</div>
		</div>
		
		<div class="col-xl-8">
			<p><?= $workflow->description ?></p>
		</div>
	</div>
	
	<h3 class="mt-3">Tasks</h3>
	<div class="row">
<?php
if (empty($stages)):
	echo '<p>This workflow has no associated tasks!</p>';
else:
?>
		<table class="table">
			<thead>
				<th scope="col"></th>
				<th scope="col">Name</th>
				<th scope="col">Summary</th>
				<th scope="col">Input</th>
				<th scope="col">
					Required
					<i class="far fa-question-circle" data-toggle="tooltip" title="Controls whether a particular task may be skipped."></i>
				</th>
			</thead>
			<tbody>
<?php
	foreach ($stages as $i=>$stage):
		foreach ($tasks as $task):
			if ($task->id == $stage->task_id):
?>
				<tr>
					<td><?= $i+1 ?>.</td>
					<td><i class="<?= $task->icon ?>"></i> <?= $task->name ?></td>
					<td class="small text-muted"><?= $task->summary ?></td>
					<td>
<?php
				switch ($task->input):
					case 'workflow':
?>
						<select class="custom-select small" onchange="setStageInput(<?= $stage->id ?>, this.value);" required>
							<option></option>
<?php
						foreach ($workflows as $workflowOpt):
							if ($workflowOpt->id == $workflow->id):
								continue;
							endif;
?>
							<option value="<?= $workflowOpt->id ?>"><?= $workflowOpt->name ?></option>
<?php
						endforeach;
?>
						</select>
<?php
					break;
				
					default:
						echo "<span class='text-muted'>n/a</span>";
				endswitch;
?>
					</td>
					<td>
						<div class="custom-control custom-switch">
							<input type="checkbox" class="custom-control-input" id="required-<?= $stage->id ?>" value="1" <?= $stage->required ? 'checked' : '' ?> onchange="toggleStage(<?= $stage->id ?>);">
							<label class="custom-control-label" for="required-<?= $stage->id ?>"></label>
						</div>
					</td>
				</tr>
<?php
				break;
			endif;
		endforeach;
	endforeach;
?>
			</tbody>
		</table>
<?php
endif;
?>
	</div>

<script>
	function toggleStage(stageId) {
		alert('toggled '+stageId);
	}

	function setStageInput(stageId, value) {
		alert('set '+stageId+' input to '+value);
	}
</script>

<?= view($config->views['footer']) ?>
