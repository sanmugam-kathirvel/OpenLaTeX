<div class="col-lg-4 col-lg-offset-2 col-md-4 col-md-offset-2 col-sm-5 col-sm-offset-1 col-xs-6 center">
	<?php 
		if($this->Session->read('Auth.User.id')){
			echo $this->Html->link('My Dashboard', '/dash', array('class' =>'btn btn-primary btn-xlg'));
		}else{
			if($this->Session->read('User.aid'))
				echo $this->Html->link('Open Your Document', '/adash?oldtemplate=open&p='.base64_encode($this->Session->read('User.aid')), array('class' =>'btn btn-primary btn-xlg'));
			else
				echo $this->Html->link('Create New Document', '/adash?template=paper', array('class' =>'btn btn-primary btn-xlg'));
		}
	?>
</div>
<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 center">
	<?php echo $this->Html->link('Templates', '/', array('class' =>'btn btn-primary btn-xlg')); ?>
</div>
	
