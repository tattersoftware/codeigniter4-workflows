<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="Matthew Gatner">
	<title>Workflows</title>

	<link rel="canonical" href="https://getbootstrap.com/docs/4.3/examples/starter-template/">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>

	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	
	<!-- FontAwesome Free -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

	<style>
		ul.alert {
			list-style: none;
		}
	</style>
</head>
<body>
	<nav class="navbar navbar-expand-md navbar-dark bg-dark">
		<a class="navbar-brand" href="<?= site_url() ?>">Home</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarsExampleDefault">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item <?= ($current=='workflows') ? 'active' : '' ?>">
					<a class="nav-link" href="<?= site_url('workflows') ?>">Workflows <?= ($current=='workflows') ? '<span class="sr-only">(current)</span>' : '' ?></a>
				</li>
				<li class="nav-item <?= ($current=='workflows') ?: 'active' ?>">
					<a class="nav-link" href="<?= site_url('tasks') ?>">Tasks <?= ($current=='tasks') ? '<span class="sr-only">(current)</span>' : '' ?></a>
				</li>
			</ul>
		</div>
	</nav>

	<main role="main" class="container my-5">

<?php
// check for error messages
if (session()->has('errors')):
?>
	<ul class="alert alert-danger">
<?php
	foreach (session('errors') as $error):
?>
		<li><?= $error ?></li>
<?php
endforeach
?>
	</ul>
<?php
endif;

// check for success message
if (session()->has('success')):
?>
	<ul class="alert alert-success">
		<li><?= session('success'); ?></li>
	</ul>
<?php
endif;
?>