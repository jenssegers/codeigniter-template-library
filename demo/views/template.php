<!doctype html>
<html>
<head>
	<title><?php echo $this->template->title->default("Gasoline CMS"); ?></title>
	<meta charset="utf-8">
	<meta name="description" content="<?php echo $this->template->description->default('Gasoline CMS is built over codeigniter'); ?>">
	<meta name="author" content="<?php echo $this->template->author->default("Bloom Developer") ?>">
	<?php echo $this->template->meta; ?>
	<?php echo $this->template->stylesheet; ?>
</head>
<body>

<div class="container">

  <?php foreach($sidebar as $item){
			echo $item->__toString();
		} 
  	
	//	echo $this->template->widget("hero_widget", array("title"=>"Hello, world!"));
	?>

  <?php echo $this->template->content; ?>

  <footer>
	<p><?php echo $this->template->copyright->default("There is no copyright"); ?></p>
  </footer>

</div>

<script src="//code.jquery.com/jquery-latest.min.js"></script>
<?php echo $this->template->javascript; ?>

</body>
</html>