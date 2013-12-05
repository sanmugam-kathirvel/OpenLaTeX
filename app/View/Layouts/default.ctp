<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>OpenLaTeX
		<?php echo 'Forum' ?>:
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		//echo $this->Html->meta('icon');
		echo $this->fetch('meta');
		echo $this->Html->css(array('bootstrap', 'codemirror', 'latex'));
		echo $this->Html->script(array('jquery-1.9.1', 'bootstrap', 'codemirror', 'stex','latex','jquery.md5', 'jquery.base64'));
	?>

</head>
<body>
	<div id="container">
		<!-- header -->
		<div id="header">
			<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
					  <span class="sr-only">Toggle navigation</span>
					  <span class="icon-bar"></span>
					  <span class="icon-bar"></span>
					  <span class="icon-bar"></span>
					</button>
					<?php echo $this->Html->link('OpenLaTeX', '/', array('class' => 'navbar-brand')); ?>
				</div>
			  <!-- Collect the nav links, forms, and other content for toggling -->
			  <div class="collapse navbar-collapse navbar-ex1-collapse">
				<ul class="nav navbar-nav">
				  <?php if($this->Session->read('Auth.User.id')): ?>
				  	 <li><?php echo $this->Html->link('Dashboard', '/dash'); ?></li>
				  <?php endif; ?>
				  <?php if(isset($files)): ?>
						 <li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Project <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><div class='add-file'>
									<span>add</span>
								</div></li>
								<?php foreach($files as $file): ?>
									<?php //var_dump($files); ?>
									<li><?php echo $file['filename']." ". ($file['can_edit'] ? ($this->Html->link('Edit', '?fn='.base64_encode($file['filename']), array('class' => 'link'))) : ''); ?></li>	
								<?php endforeach; ?>
							</ul>
						</li>
					<?php endif; ?>
				  <li><?php echo $this->Html->link('Templates', '/templates'); ?></li>
				</ul>
				<?php if(isset($output) && isset($output['errors'])): ?>
					<ul class="nav navbar-nav">
					  <li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Errors<span class="badge errors"><?php echo count($output['errors']); ?></span></a>
						<ul class="dropdown-menu fixed-height">
							<?php echo $this->Html->link('View log file', '/tex/users/1/526b5ee1b75ad/main.log', array('class' =>'link sticky-header', 'target' => '_blank')); ?>
							<?php foreach($output['errors'] as $error): ?>
								<li><a href="#"><?php echo $error; ?></a></li>
							<?php endforeach; ?>
						</ul>
					  </li>
					</ul>
				<?php endif; ?>
				<?php if(isset($output) && isset($output['warnings'])): ?>
					<ul class="nav navbar-nav">
					  <li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Warnings<span class="badge warnings"><?php echo count($output['warnings']); ?></span></a>
						<ul class="dropdown-menu fixed-height">
							<?php foreach($output['warnings'] as $warning): ?>
								<li><a href="#"><?php echo $warning; ?></a></li>
							<?php endforeach; ?>
						</ul>
					  </li>
					</ul>
				<?php endif; ?>
				<!-- resources -->
				<ul class="nav navbar-nav">
					  <li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Resources</a>
						<ul class="dropdown-menu">
							<?php echo $this->element('resources'); ?>
							<li><a href="#"></a></li>
						</ul>
					  </li>
					</ul>
				<ul class="nav navbar-nav navbar-right user-details">
				  <?php if($this->Session->read('Auth.User.id')): ?>
				    <li><a href='#'><?php echo $this->Session->read('Auth.User.username'); ?></a></li>
				  	<li><a href="logout">Logout</a></li>
				  <?php else: ?>
				  	<li><a href="login">Login</a></li>
				  <?php endif; ?>
				</ul>
			  </div><!-- /.navbar-collapse -->
			</nav>
		</div>
		<div style="height:50px;"></div>
		<?php if ($this->request->params['action'] == 'home'): ?>
			<div id="content" class="bs-header">
			  <div class="container">
				<h1>Instructions</h1>
				<p>Over a dozen reusable components built to provide iconography, dropdowns, navigation, alerts, popovers, and much more.</p>
			  </div>
			</div>
		<?php endif; ?>
		<!-- header end -->
		<div id="content" class="bs-docs-container">
			<!--<div class="row">
				<div class="col-md-12"> -->
					<?php echo $this->Session->flash(); ?>

					<?php echo $this->fetch('content'); ?>
				<!--</div>
			</div> -->
		</div>
	</div>
	<div id="footer" class="navbar-fixed-bottom navbar-inverse">
		  <div class="footer">
		    <p class="muted credit">OpenLaTeX</p>
		  </div>
    </div>
</body>
</html>
