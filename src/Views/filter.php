<?= $this->extend($layout) ?>
<?= $this->section('main') ?>

	<h2>Job not available</h2>

	<div class="alert alert-info">
		Your job "<?= $job->name ?>" is currently waiting for manager input.
	</div>

	<a class="btn btn-primary" href="<?= site_url(config('Workflows')->routeBase . '/show/' . $job->id) ?>">View Job</a>
	<a class="btn btn-link" href="<?= base_url() ?>">Home</a>

<?= $this->endSection() ?>
