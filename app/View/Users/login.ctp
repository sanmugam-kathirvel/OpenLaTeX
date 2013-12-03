<div class="login-container">
	<div class="login-form">
		<h2>Login</h2>
			<?php echo $this->Session->flash('auth'); ?>
			<?php echo $this->Session->flash(); ?>
		<?php echo $this->Form->create('User', array('class' => 'form'));
    	echo $this->Form->input('username', array('label' => false, 'placeholder' => 'Username', 'class' => 'form-control'));
      echo $this->Form->input('password', array('label' => false, 'placeholder' => 'Password', 'class' => 'form-control'));
      echo $this->Form->end(array('label' => 'Login', 'class' => 'btn btn-primary'));
		?>

	</div>
</div>
