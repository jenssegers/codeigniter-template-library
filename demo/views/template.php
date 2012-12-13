<!doctype html>
<html>
<head>
	<title><?php echo $this->template->title->default("Default title"); ?></title>
	<meta charset="utf-8">
	<meta name="description" content="<?php echo $this->template->description; ?>">
	<meta name="author" content="">
	<?php echo $this->template->meta; ?>
	<?php echo $this->template->stylesheet; ?>
</head>
<body>

<?php 
	// This is an example to show that you can load stuff from inside the template file
	echo $this->template->widget("navigation", array('title' => 'Project name'));
?>

<div class="container" style="margin-top: 60px;">

  <?php
  	// This is the main content partial
  	echo $this->template->content;
  ?>

  <hr>

  <footer>
	<p>
		<?php 
			// Show the footer partial, and prepend copyright message
			echo $this->template->footer->prepend("&copy; Special Company 2012 - ");
		?>
	</p>
  </footer>

</div>

<script src="//code.jquery.com/jquery-latest.min.js"></script>
<?php echo $this->template->javascript; ?>

</body>
</html>