<?= view($config->views['header']) ?>

	<h2>Job removed</h2>

	<div class="alert alert-info">
		Your job "<?= $job->name ?>" has been removed.
	</div>
	
	<p><?= anchor('', 'Home') ?></p>

<?= view($config->views['footer']) ?>
