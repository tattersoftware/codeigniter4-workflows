<?= $this->extend($layout, ['menu' => 'workflows']) ?>
<?= $this->section('main') ?>

	<a class="btn btn-primary float-right" href="<?= site_url('workflows/new') ?>" role="button"><i class="fas fa-plus-circle"></i> New workflow</a>
	<h2>Workflows</h2>

	<?php if (empty($workflows)): ?>

	<p>There are no workflows defined. Try adding one!</p>

	<?php return $this->endSection(); endif; ?>

	<table class="table">
		<thead>
			<tr>
				<th scope="col">#</th>
				<th scope="col">Name</th>
				<th scope="col">Category</th>
				<th scope="col">Icon</th>
				<th scope="col">Summary</th>
				<th scope="col">Actions</th>
			</tr>
		</thead>
		<tbody>

			<?php foreach ($workflows as $workflow): ?>

			<tr>
				<th scope="row"><?= $workflow->id ?></th>
				<td><?= anchor('workflows/' . $workflow->id, $workflow->name) ?></td>
				<td><?= $workflow->category ?></td>
				<td><?= $workflow->icon ?></td>
				<td><?= $workflow->summary ?></td>
				<td><?= isset($stages[$workflow->id]) ? count($stages[$workflow->id]) : 0 ?></td>
			</tr>

			<?php endforeach; ?>

		</tbody>
	</table>

<?= $this->endSection() ?>
