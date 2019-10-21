<?php
$menu = $menu ?? '';
?><!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Job task control through dynamic workflows, for CodeIgniter 4">
	<meta name="author" content="Tatter Software">

	<title>Workflows</title>

	<link rel="canonical" href="https://getbootstrap.com/docs/4.3/examples/starter-template/">

	<!-- Assets from Manifests -->
	<?= service('assets')->tag('vendor/jquery/jquery.min.js') ?>
	
	<?= service('assets')->tag('vendor/bootstrap/bootstrap.min.css') ?>
	
	<?= service('assets')->tag('vendor/font-awesome/css/all.min.css') ?>

	<?= $this->renderSection('headerAssets') ?>
</head>
<body>

<nav class="navbar navbar-expand-md navbar-dark bg-dark">
	<a class="navbar-brand" href="<?= site_url() ?>">Home</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="navbarsExampleDefault">
		<ul class="navbar-nav mr-auto">
			<li class="nav-item <?= $menu == 'workflows' ? 'active' : '' ?>">
				<a class="nav-link" href="<?= site_url('workflows') ?>">Workflows <?= $menu == 'workflows' ? '<span class="sr-only">(current)</span>' : '' ?></a>
			</li>
			<li class="nav-item <?= $menu == 'tasks' ? 'active' : '' ?>">
				<a class="nav-link" href="<?= site_url('tasks') ?>">Tasks <?= $menu == 'tasks' ? '<span class="sr-only">(current)</span>' : '' ?></a>
			</li>
		</ul>
	</div>
</nav>
	
<?= service('alerts')->display() ?>

<main role="main" class="container my-5">

	<?= $this->renderSection('main') ?>

</main><!-- /.container -->

<!-- Assets from Manifests -->
<?= service('assets')->tag('vendor/bootstrap/bootstrap.bundle.min.js') ?>

<?= service('assets')->tag('vendor/sortablejs/Sortable.min.js') ?>

<script>
	$(function () {
		$('[data-toggle="tooltip"]').tooltip();
	});
</script>

<?= $this->renderSection('footerAssets') ?>

</body>
</html>
