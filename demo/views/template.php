<!DOCTYPE html>
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

<div class="container">

  <?php echo $this->template->widget("hero_widget", array("title"=>"Hello, world!")); ?>

  <?php echo $this->template->content; ?>

  <footer>
	<p><?php echo $this->template->copyright->default("There is no copyright"); ?></p>
  </footer>

</div>

<script src="http://code.jquery.com/jquery-1.7.1.min.js">
<?php echo $this->template->javascript; ?>

</body>
</html>