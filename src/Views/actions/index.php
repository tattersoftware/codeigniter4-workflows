<?= $this->extend($layout, ['menu' => 'actions']) ?>
<?= $this->section('main') ?>

	<h2>Actions</h2>

	<?php if (empty($actions)): ?>

	<p>There are no actions registered! Try running <code>php spark actions:register</code> from the command line.</p>

	<?php return $this->endSection(); endif; ?>

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

			<?php foreach ($actions as $action): ?>

			<tr>
				<th scope="row"><?= $action->id ?></th>
				<td><i class="<?= $action->icon ?>"></i> <?= $action->name ?></td>
				<td><?= $action->category ?></td>
				<td><?= $action->class ?></td>
				<td><?= $action->uid ?></td>
				<td><?= $action->summary ?></td>
			</tr>

			<?php endforeach; ?>

		</tbody>
	</table>

<?= $this->endSection() ?>
