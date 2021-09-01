<?= $this->extend($layout, ['menu' => 'workflows']) ?>
<?= $this->section('main') ?>

	<?php if (empty($workflow)): ?>

<p>Unable to locate that workflow!</p>

	<?php return $this->endSection(); endif; ?>

	<a class="btn btn-primary float-right" href="<?= site_url($config->routeBase . '/new/' . $workflow->id) ?>" role="button"><i class="fas fa-rocket"></i> Launch</a>
	<h2>Workflow</h2>

	<h3 class="mt-3">Details</h3>
	<div class="row mt-4">
		<div id="workflow-card" class="col-xl-4">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title"><i class="<?= $workflow->icon ?: 'far fa-circle' ?>"></i> <?= $workflow->name ?></h5>
					<h6 class="card-subtitle mb-2 text-muted"><?= $workflow->category ?: 'No' ?> Category</h6>
					<p><?= $workflow->role ?: '<em>Unrestricted</em>'?></p>
					<p class="card-text"><?= $workflow->summary ?></p>

					<a href="#" class="card-link btn btn-primary" onclick="$('#workflow-card .card').toggle(); return false;">Edit</a>
					<form class="card-link float-right" name="delete-workflow" action="<?= site_url('workflows/' . $workflow->id . '/delete') ?>" method="post">
						<button class="btn btn-link" tyoe="submit"><i class="fas fa-trash"></i> Delete</button>
					</form>
				</div>
			</div>
			<div class="card" style="display:none;">
				<form name="update-workflow" action="<?= site_url('workflows/' . $workflow->id) ?>" method="post">
					<div class="card-body">
						<h5 class="card-title">
							<input name="icon" type="text" class="form-control" id="icon" aria-describedby="iconHelp" placeholder="Workflow icon" value="<?= $workflow->icon ?>">
							<input name="name" type="text" class="form-control" id="name" placeholder="Workflow name" value="<?= $workflow->name ?>" required>
						</h5>
						<input name="role" type="text" class="form-control" id="role" placeholder="Role restriction" value="<?= $workflow->role ?>">
						<input name="category" type="text" class="form-control" id="category" placeholder="Workflow category" value="<?= $workflow->category ?>">
						<p class="card-text">
							<input name="summary" type="text" class="form-control" id="icon" placeholder="Workflow summary" value="<?= $workflow->summary ?>" required>
						</p>

						<input class="card-link btn btn-primary" type="submit" value="Submit">
						<a href="#" class="card-link" onclick="$('#workflow-card .card').toggle(); return false;">Cancel</a>
					</div>
				</form>
			</div>
		</div>

		<div class="col-xl-8" id="response">
			<p><?= $workflow->description ?></p>
		</div>
	</div>

	<h3 class="mt-3">Actions</h3>
	<div class="row">

		<?php if (empty($stages)): ?>

		<p>This workflow has no associated actions!</p>

		<?php else: ?>

		<table class="table">
			<thead>
				<th scope="col"></th>
				<th scope="col">Name</th>
				<th scope="col">Summary</th>
				<th scope="col">Input</th>
				<th scope="col">
					Required
					<i class="far fa-question-circle" data-toggle="tooltip" title="Controls whether a particular action may be skipped."></i>
				</th>
			</thead>
			<tbody>

				<?php $i = 1; ?>
				<?php foreach ($stages as $stage): ?>
				<?php foreach ($actions as $action): ?>
				<?php if ($action->id === $stage->action_id): ?>

				<tr>
					<td><?= $i++ ?>.</td>
					<td><i class="<?= $action->icon ?>"></i> <?= $action->name ?></td>
					<td class="small text-muted"><?= $action->summary ?></td>
					<td>

					<?php switch ($action->input): case 'workflow': ?>

						<select class="custom-select small" onchange="return setStageInput(<?= $stage->id ?>, this.value);" required>
							<option></option>

							<?php foreach ($workflows as $workflowOpt): ?>
								<?php if ($workflowOpt->id === $workflow->id): continue; endif; ?>

							<option value="<?= $workflowOpt->id ?>" <?= ($workflowOpt->id === $stage->input) ? 'selected' : '' ?>><?= $workflowOpt->name ?></option>

							<?php endforeach; ?>

						</select>

						<?php break;

case '': break;

default: ?>

						<input name="input" type="<?= $action->input ?>" class="form-control" value="<?= $stage->input ?>" onchange="return setStageInput(<?= $stage->id ?>, this.value);" required>

					<?php endswitch; ?>

					</td>
					<td>
						<div class="custom-control custom-switch">
							<input type="checkbox" class="custom-control-input" id="required-<?= $stage->id ?>" value="1" <?= $stage->required ? 'checked' : '' ?> onclick="return setStageRequired(<?= $stage->id ?>, this.checked);">
							<label class="custom-control-label" for="required-<?= $stage->id ?>"></label>
						</div>
					</td>
				</tr>

				<?php break; endif; ?>
				<?php endforeach; ?>
				<?php endforeach; ?>

			</tbody>
		</table>

		<?php endif; ?>

	</div>

<?= $this->endSection() ?>
<?= $this->section('footerAssets') ?>

<script>
	var baseUrl = '<?= base_url() ?>';

	function setStageRequired(stageId, checked) {
		checked = checked ? 1 : 0;
		return updateStage(stageId, { required: checked });
	}

	function setStageInput(stageId, value) {
		return updateStage(stageId, { input: value });
	}

	function updateStage(stageId, data) {
		// set the method spoof
		data._method = "PUT";

		$.ajax({
			type: "POST",
			url: baseUrl + "stages/" + stageId,
			data: data,
			datatype: "text"
		}).done(function( data ) {
			if (data) {
				alert(data);
				return false;
			}

			return true;
		});
	}
</script>

<?= $this->endSection() ?>
