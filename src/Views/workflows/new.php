<?= $this->extend($layout, ['menu' => 'workflows']) ?>
<?= $this->section('main') ?>

<style>
.remove-icon {
	cursor: pointer;
}
.sort-handle {
	cursor: move;
	cursor: -webkit-grabbing;
	margin-right: 20px;
}
</style>

	<h2>New Workflow</h2>

	<?php if (empty($actions)): ?>

	<p>There are no actions defined. Please add some actions before defining a workflow.</p>

	<?php return $this->endSection(); endif; ?>

	<form name="create-workflow" action="<?= site_url('workflows') ?>" method="post" onsubmit="this.actions.value = sortable.toArray();">
		<input class="btn btn-primary float-right" type="submit" value="Submit">
		<input name="actions" type="hidden" value="" />
		
		<div class="row mt-4">
			<div class="col-sm-4">
				<h3>Details</h3>
				<div class="form-group">
					<label for="category">Category</label>
					<input name="category" type="text" class="form-control" id="category" aria-describedby="categoryHelp" placeholder="Workflow category" value="<?= old('category') ?>">
					<small id="categoryHelp" class="form-text text-muted">A generalized group to organize workflows.</small>
				</div>
				<div class="form-group">
					<label for="name">Name</label>
					<input name="name" type="text" class="form-control" id="name" aria-describedby="nameHelp" placeholder="Workflow name" value="<?= old('name') ?>" required>
					<small id="nameHelp" class="form-text text-muted">A short descriptive name to identify this workflow.</small>
				</div>
				<div class="form-group">
					<label for="role">Role</label>
					<input name="role" type="text" class="form-control" id="role" aria-describedby="roleHelp" placeholder="Role restriction" value="<?= old('role') ?>">
					<small id="roleHelp" class="form-text text-muted">An optional role to check if restricting this workflow.</small>
				</div>
				<div class="form-group">
					<label for="icon">Icon</label>
					<input name="icon" type="text" class="form-control" id="icon" aria-describedby="iconHelp" placeholder="Workflow icon" value="<?= old('icon') ?>">
					<small id="iconHelp" class="form-text text-muted">An icon class for this workflow (usually: FontAwesome).</small>
				</div>
				<div class="form-group">
					<label for="summary">Summary</label>
					<input name="summary" type="text" class="form-control" id="icon" aria-describedby="summaryHelp" placeholder="Workflow summary" value="<?= old('summary') ?>" required>
					<small id="summaryHelp" class="form-text text-muted">A brief summary of this workflow's usage.</small>
				</div>
				<div class="form-group">
					<label for="description">Description</label>
					<textarea name="description" class="form-control" id="description" rows="3" aria-describedby="descriptionHelp" placeholder="Workflow description"><?= old('description') ?></textarea>
					<small id="descriptionHelp" class="form-text text-muted">A full description or instructions for using this workflow.</small>
				</div>
			</div>
		
			<div class="col-sm-8">
				<h3>Actions</h3>
				<div id="actionsSelect" class="mb-4">

					<?php foreach ($actions as $action): ?>

					<button type="button" class="btn btn-outline-primary" onclick="addAction(<?= $action->id ?>);">
						<i class="fas fa-plus-circle"></i>
						<?= $action->name ?>
						<small class="text-muted">(<?= $action->uid ?>)</small>
					</button>

					<?php endforeach; ?>

				</div>
			
				<div id="actionsList" class="list-group">
				
				<?php foreach (explode(',', old('actions')) as $actionId): ?>
				<?php foreach ($actions as $action): ?>
				<?php if ($action->id == $actionId): ?>

					<div class="list-group-item" data-id="<?= $action->id ?>">
						<span class="remove-icon float-right" onclick="this.parentNode.remove();"><i class="fas fa-minus-circle"></i></span>
						<span class="sort-handle" aria-hidden="true"><i class="fas fa-arrows-alt-v"></i></span>
						<i class="fas <?= $action->icon ?>"></i>
						<span class="font-weight-bold mr-3"><?= $action->name ?></span>
						<small class="text-muted"><?= $action->summary ?></small>
					</div>

				<?php break; endif; ?>
				<?php endforeach; ?>
				<?php endforeach; ?>

				</div>
			</div>
		</form>
	</div>

<?= $this->endSection() ?>
<?= $this->section('footerAssets') ?>

<script>
var sortable;
$(document).ready(function() {
	var sortList = document.getElementById('actionsList');
	sortable = new Sortable.create(sortList, {
	  handle: '.sort-handle',
	  animation: 150
	});
});

function addAction(actionId) {
	action = actions[actionId];
	
	html  = '<div class="list-group-item" data-id="' + action['id'] + '" onclick="this.remove();"> ';
	html += '<span class="remove-icon float-right" onclick="this.parentNode.remove();"><i class="fas fa-minus-circle"></i></span>';
	html += '<span class="sort-handle" aria-hidden="true"><i class="fas fa-arrows-alt-v"></i></span> '
	html += '<i class="fas ' + action['icon'] + '"></i> ';
	html += '<span class="font-weight-bold mr-3">' + action['name'] +'</span> ';
	html += '<small class="text-muted">' + action['summary'] + '</small> ';
	html += '</div>';
	
	$('#actionsList').append(html);
}

var actions = <?= $json ?>;

</script>

<?= $this->endSection() ?>
