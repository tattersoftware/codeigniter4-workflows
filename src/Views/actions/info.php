<?= $this->extend($layout, ['menu' => 'jobs']) ?>
<?= $this->section('headerAssets') ?>

<script>
	var baseUrl = '<?= base_url() ?>';
</script>

<?= $this->endSection() ?>
<?= $this->section('main') ?>

<div class="container">

	<?php if (empty($job)): ?>

	<p>Unable to locate that job!</p>

	<?php else: ?>

	<h2>Job info</h2>

	<?php if (! $job->stage->required && $stage = $job->next()): ?>

		<a class="btn btn-link float-right" href="<?= site_url($stage->action->getRoute($job->id)) ?>" role="button"><i class="fas fa-arrow-circle-right"></i> Skip</a>

	<?php endif; ?>

	<form name="update-job" action="<?= site_url("{$config->routeBase}/info/{$job->id}") ?>" method="post">
		<input class="btn btn-primary float-right" type="submit" value="Submit">

		<div class="row mt-4">
			<div class="col-sm-8">
				<div class="form-group">
					<label for="name">Name</label>
					<input name="name" type="text" class="form-control" id="name" aria-describedby="nameHelp" placeholder="Job name" value="<?= old('name', $job->name) ?>" required>
					<small id="nameHelp" class="form-text text-muted">A short descriptive name to identify this job.</small>
				</div>
				<div class="form-group">
					<label for="summary">Summary</label>
					<input name="summary" type="text" class="form-control" id="icon" aria-describedby="summaryHelp" placeholder="Job summary" value="<?= old('summary', $job->summary) ?>">
					<small id="summaryHelp" class="form-text text-muted">A brief summary of this job.</small>
				</div>
			</div>
		</div>
	</div>

	<?php endif; ?>
</div>

<?= $this->endSection() ?>
