<?= $this->extend($layout) ?>
<?= $this->section('main') ?>

	<h2>Job not available</h2>

	<div class="alert alert-info">
		Your job "<?= $job->name ?>" is currently waiting for manager input.
	</div>
	
	<p><?= anchor('', 'Home') ?></p>

<?= $this->endSection() ?>
