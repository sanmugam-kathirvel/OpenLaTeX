<div id='resources-container'>
	<?php
		echo $this->Form->create('Pages', array('action' => 'resources', 'enctype' => 'multipart/form-data'));
			echo $this->Form->input('resources', array('type' => 'file', 'label' => FALSE));
		echo $this->Form->end('upload');
	?>
</div>
