<?php $this->extend($layout); ?>
<?php $this->section('main'); ?>

	<h2>Actions</h2>

	<?php if (empty($actions)): ?>

	<p>There are no actions registered! Try running <code>php spark actions:register</code> from the command line.</p>

	<?php return $this->endSection(); endif; ?>

	<table class="table">
		<thead>
			<tr>
				<th scope="col"></th>
				<th scope="col">ID</th>
				<th scope="col">Name</th>
				<th scope="col">Category</th>
				<th scope="col">Class</th>
				<th scope="col">Summary</th>
				<th scope="col">Role</th>
			</tr>
		</thead>
		<tbody>

			<?php foreach ($actions as $action): ?>

			<tr>
				<td><i class="<?= $action['icon'] ?>"></i></td>
				<th scope="row"><?= $action['id'] ?></th>
				<td><?= $action['name'] ?></td>
				<td><?= $action['category'] ?></td>
				<td><?= $action['class'] ?></td>
				<td><?= $action['summary'] ?></td>
				<td><?= $action['role'] ?></td>
			</tr>

			<?php endforeach; ?>

		</tbody>
	</table>

<?php $this->endSection(); ?>
