<?php $this->extend($layout); ?>
<?php $this->section('main'); ?>

<div class="container">

	<h2>Job acceptance</h2>

	<?php if ($job->maySkip() && $stage = $job->getStage()->getNext()): ?>

		<a class="btn btn-link float-right" href="<?= site_url($stage->getRoute() . $job->id) ?>" role="button"><i class="fas fa-arrow-circle-right"></i> Skip</a>

	<?php endif; ?>

	<form name="button-job" method="post">
		<p class="mb-4"><?= $prompt ?>.</p>
		<input class="btn btn-primary" name="submit" type="submit" value="Submit">
	</form>
</div>

<?php $this->endSection(); ?>
