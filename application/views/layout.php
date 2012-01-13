<!DOCTYPE html>
<html>
<head>
	<title><?php echo $this->template->title->default("Welcome!"); ?></title>
	<meta charset="utf-8">
	<meta name="description" content="<?php echo $this->template->description; ?>">
	<meta name="author" content="">
	<?php echo $this->template->meta; ?>
	<?php echo $this->template->stylesheet; ?>
</head>
<body>

<div class="container">

  <?php echo $this->template->widget("hero", array("title"=>"Hello, world!")); ?>

  <?php echo $this->template->content; ?>

  <footer>
	<p><?php echo $this->template->copyright->default("There is no copyright"); ?></p>
  </footer>

</div>

<script type="text/javascript">
	var base_url = "<?php echo base_url(); ?>";
	var site_url = "<?php echo site_url(); ?>";
</script>
<?php echo $this->template->javascript; ?>

</body>
</html>