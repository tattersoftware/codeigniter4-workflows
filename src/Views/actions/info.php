<?php $this->extend($layout); ?>
<?php $this->section('main'); ?>

<div class="container">

	<h4>Basic info</h4>

	<?php if (! $job->getStage()->required && $stage = $job->getStage()->getNext()): ?>

		<a class="btn btn-link float-right" href="<?= site_url($stage->getRoute() . $job->id) ?>" role="button"><i class="fas fa-arrow-circle-right"></i> Skip</a>

	<?php endif; ?>

	<form name="update-job" method="post">
		<input class="btn btn-primary float-right" type="submit" value="Submit">

		<div class="row">
			<div class="col-sm-8">
				<div class="form-group">
					<label for="name">Job name<span class="badge badge-warning ml-2">Required</span></label>
					<input name="name" type="text" class="form-control" id="name" aria-describedby="nameHelp" placeholder="Job name" value="<?= old('name', $job->name) ?>" required>
					<small id="nameHelp" class="form-text text-muted">A short descriptive name to identify this job.</small>
				</div>
				<div class="form-group">
					<label for="summary">Job description</label>
					<input name="summary" type="text" class="form-control" id="icon" aria-describedby="summaryHelp" placeholder="Job summary" value="<?= old('summary', $job->summary) ?>">
					<small id="summaryHelp" class="form-text text-muted">A brief summary of this job.</small>
				</div>
			</div>
		</div>
	</form>
</div>

<?php $this->endSection(); ?>
