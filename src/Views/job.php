<?= $this->extend($layout) ?>
<?= $this->section('main') ?>

	<h2><?= $job->name ?></h2>
	<p><?= $job->summary ?></p>

	<a class="btn btn-primary" href="<?= site_url(config('Workflows')->routeBase . '/' . $job->id) ?>">Continue Job</a>

<?= $this->endSection() ?>
