	<div class="container">
	<p>Welcome to OpenLaTeX Template</p>
	<?php $user = $this->Session->read('User'); ?>
	<ul class='display-grid'>
		<?php foreach($templates as $template): ?>
			<li><?php echo $this->Html->link($this->Html->image('/tpl/'.$template['Template']['id'].'/tpl.png', array('height' => '200px')), '/usetemplate?tpl='.$template['Template']['name'], array('class' => 'link', 'escape' => false)); ?>
				<p class='center'><?php echo $template['Template']['name']; ?></p>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
