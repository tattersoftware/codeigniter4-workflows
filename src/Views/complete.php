<?= $this->extend($layout) ?>
<?= $this->section('main') ?>

	<h2>Job complete</h2>

	<div class="alert alert-success">
		Your job "<?= $job->name ?>" completed successfully.
	</div>
	
	<p><?= anchor('', 'Home') ?></p>

<?= $this->endSection() ?>
