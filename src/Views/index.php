<?= view($config->views['header']) ?>

	<h2>Workflows</h2>
<?php
if (empty($workflows)):
?>
	<p>There are no workflows defined. Try adding one!</p>
<?php
else:
?>
	<table class="table">
		<thead>
			<tr>
				<th scope="col">#</th>
				<th scope="col">Name</th>
				<th scope="col">Category</th>
				<th scope="col">Icon</th>
				<th scope="col">Summary</th>
			</tr>
		</thead>
		<tbody>
<?php
	foreach ($workflows as $workflow):
?>
		<tr>
			<th scope="row"><?= $workflow->id ?></th>
			<td><?= $workflow->name ?></td>
			<td><?= $workflow->category ?></td>
			<td><?= $workflow->icon ?></td>
			<td><?= $workflow->summary ?></td>
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
