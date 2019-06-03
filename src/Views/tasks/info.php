<?= view($config->views['header'], ['current' => 'tasks']) ?>

<?php
if (empty($job)):
	echo '<p>Unable to locate that job!</p>';
	return;
endif;
?>
	<h2>Job info</h2>


	<form name="update-job" action="<?= site_url("{$config->routeBase}/info/{$job->id}") ?>" method="post">
		<input class="btn btn-primary float-right" type="submit" value="Submit">
		<input type="hidden" name="_method" value="PUT" />
		
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

<script>
	var baseUrl = '<?= base_url() ?>';
</script>

<?= view($config->views['footer']) ?>
