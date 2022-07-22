<?php $this->extend($layout); ?>
<?php $this->section('main'); ?>

	<h2><?= $job->name ?></h2>
	<p><?= $job->summary ?></p>

    <?php if ($stage = $job->getStage()): ?>
	<a class="btn btn-primary" href="<?= site_url($stage->getRoute() . $job->id) ?>">Continue Job</a>
	<?php else: ?>
	<p class="text-muted">This job is complete.</p>
	<?php endif; ?>

<?php $this->endSection(); ?>
