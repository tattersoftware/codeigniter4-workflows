<?= $this->extend($layout) ?>
<?= $this->section('main') ?>

<div class="container">

	<h2>Job acceptance</h2>

	<?php if (! $job->stage->required && $stage = $job->next()): ?>

		<a class="btn btn-link float-right" href="<?= site_url($stage->action->getRoute($job->id)) ?>" role="button"><i class="fas fa-arrow-circle-right"></i> Skip</a>

	<?php endif; ?>

	<form name="button-job" action="<?= site_url("{$config->routeBase}/button/{$job->id}") ?>" method="post">
		<p><?= $prompt ?>.</p>
		<input class="btn btn-primary float-right" type="submit" value="Submit">
	</form>
</div>

<?= $this->endSection() ?>
