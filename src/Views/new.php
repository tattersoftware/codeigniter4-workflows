<?= view($config->views['header']) ?>

<style>
.sort-handle {
	cursor: move;
	cursor: -webkit-grabbing;
	margin-right: 20px;
}
</style>

	<h2>New Workflow</h2>
<?php
if (empty($tasks)):
?>
	<p>There are no tasks defined. Please add some tasks before defining a workflow.</p>
<?php
	return;
endif;
?>
	<div class="row">
		<div class="col-sm-4">
			<h3>Details</h3>
			<form name="create-workflow" action="<?= site_url('workflows') ?>" metho="post">
				<div class="form-group">
					<label for="category">Category</label>
					<input type="text" class="form-control" id="category" aria-describedby="categoryHelp" placeholder="Workflow category">
					<small id="categoryHelp" class="form-text text-muted">A generalized group to organize workflows.</small>
				</div>
				<div class="form-group">
					<label for="name">Name</label>
					<input type="text" class="form-control" id="name" aria-describedby="nameHelp" placeholder="Workflow name">
					<small id="nameHelp" class="form-text text-muted">A short descriptive name to identify this workflow.</small>
				</div>
				<div class="form-group">
					<label for="name">Icon</label>
					<input type="text" class="form-control" id="icon" aria-describedby="iconHelp" placeholder="Workflow icon">
					<small id="iconHelp" class="form-text text-muted">A FontAwesome (or other) icon name for this workflow.</small>
				</div>
				<div class="form-group">
					<label for="name">Summary</label>
					<input type="text" class="form-control" id="icon" aria-describedby="summaryHelp" placeholder="Workflow summary">
					<small id="summaryHelp" class="form-text text-muted">A brief summary of this workflow's usage.</small>
				</div>
				<div class="form-group">
					<label for="description">Description</label>
					<textarea class="form-control" id="description" rows="3" aria-describedby="descriptionHelp" placeholder="Workflow description"></textarea>
					<small id="descriptionHelp" class="form-text text-muted">A full description or instructions for using this workflow.</small>
				</div>
			</form>
		</div>
		
		<div class="col-sm-8">
			<h3>Tasks</h3>
			<div id="tasksList" class="list-group">
<?php
foreach ($tasks as $task):
?>
				<div class="list-group-item">
					<span class="sort-handle" aria-hidden="true">::</span>
					<?= $task->name ?>
					<span class="badge"><?= $task->id ?></span>
				</div>
<?php
endforeach;
?>
			</div>
		</div>
		
	</div>

<script>
$(document).ready(function() {
	var sortList = document.getElementById('tasksList');
	new Sortable.create(sortList, {
	  handle: '.sort-handle',
	  animation: 150
	});
});
</script>

<?= view($config->views['footer']) ?>
