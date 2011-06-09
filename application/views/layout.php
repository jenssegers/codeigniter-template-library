<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>
		<?php echo $this->template->title; ?>
	</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php echo $this->template->meta; ?>
	<?php echo $this->template->stylesheet; ?>
</head>
<body>

<div id="top">
	<div id="menu">
		<?php echo $this->template->menu->view("menu", array("key"=>"value")); ?>
	</div>
</div>

<div id="container">
	<div id="content">
	
	<?php
	/* Load/create it using the library method:
	 * echo $this->template->partial("content", "Default content"); */
	
	/* Get it directly from the template library:
	 * echo $this->template->content;*/
	
	/* Manipulate the partial before printing:
	 * echo $this->template->content->append("This will be appended"); */
	
	/* Partials are passed as data to the view/parse files: */
	echo $content; ?>
	
	<!-- When using parser: {content} -->
	</div>
	<div id="sidebar">
		<?php echo $this->template->sidebar; ?>
	</div>
	
	<div id="footer">Template rendered in {elapsed_time} seconds</div>
</div>



<?php echo $this->template->javascript; ?>
</body>
</html>