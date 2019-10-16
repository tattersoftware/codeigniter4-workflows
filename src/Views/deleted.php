<?= $this->extend($layout) ?>
<?= $this->section('main') ?>

	<h2>Job removed</h2>

	<div class="alert alert-info">
		Your job "<?= $job->name ?>" has been removed.
	</div>
	
	<p><?= anchor('', 'Home') ?></p>

<?= $this->endSection() ?>
