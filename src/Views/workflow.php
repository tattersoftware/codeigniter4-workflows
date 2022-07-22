<?php $this->extend($layout); ?>
<?php $this->section('main'); ?>

	<h2>Select a workflow</h2>

	<ul>
		<?php foreach ($workflows as $workflow): ?>
		<li><?= anchor(site_url('jobs/new/' . $workflow->id), $workflow->name) ?></li>
		<?php endforeach; ?>
	</ul>

	<p><?= anchor('', 'Home') ?></p>

<?php $this->endSection(); ?>
