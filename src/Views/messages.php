<?= $this->extend($layout) ?>
<?= $this->section('main') ?>

	<h2><?= $header ?></h2>

	<div class="alert alert-<?= $class ?>">
		<?= $message ?>
	</div>
	
	<p>
		<?= if ($job !== null): ?>
		<a class="btn btn-primary" href="<?= $job->getUrl() ?>">View Job</a>
		<?php endif; ?>

		<a class="btn btn-link" href="<?= base_url() ?>">Home</a>
	</p>

<?= $this->endSection() ?>
