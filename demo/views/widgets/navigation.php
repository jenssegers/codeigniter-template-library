<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <a class="brand" href="#"><?php echo $title; ?></a>
      <ul class="nav">
        <?php foreach($items as $item): ?>
        <li><a href="#<?php echo $item; ?>"><?php echo $item; ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>