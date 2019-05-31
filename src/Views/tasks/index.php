<?= view($config->views['header'], ['current' => 'tasks']) ?>

	<h2>Tasks</h2>

<?php
if (empty($tasks)):
?>
	<p>There are no tasks registered! Try running `php spark tasks:register`.</p>
<?php
else:
?>
	<table class="table">
		<thead>
			<tr>
				<th scope="col">#</th>
				<th scope="col">Name</th>
				<th scope="col">Category</th>
				<th scope="col">Class</th>
				<th scope="col">UID</th>
				<th scope="col">Summary</th>
			</tr>
		</thead>
		<tbody>
<?php
	foreach ($tasks as $task):
?>
		<tr>
			<th scope="row"><?= $task->id ?></th>
			<td><i class="<?= $task->icon ?>"></i> <?= $task->name ?></td>
			<td><?= $task->category ?></td>
			<td><?= $task->class ?></td>
			<td><?= $task->uid ?></td>
			<td><?= $task->summary ?></td>
		</tr>
<?php
	endforeach;
?>
		</tbody>
	</table>
<?php
endif;
?>

<?= view($config->views['footer']) ?>