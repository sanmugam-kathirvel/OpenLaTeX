<div class="container">
	<?php if($dirlist): ?>
		<p>Welcome to OpenLaTeX!</p>
		<p>To Create new Project, <?php echo $this->Html->link('create a new project', '/dash?template=paper', array('class' => 'link')); ?></p>
		<p>or choose the existing projects.</p>
		<ul>
		<?php 
			$i = 1; 
			foreach($dirlist as $dir): ?>
			<li><?php echo $this->Html->link('Project '.$i, '/dash?oldtemplate=open&p='.base64_encode($dir)); ?> | <?php echo $this->Html->link('Delete', '/delete?p='.base64_encode($dir)); ?> </li>
		<?php 
				$i++;
			endforeach; 
		?>
		</ul>
	<?php else: ?>
		<p>Welcome to OpenLaTeX!</p>
		<p>To get started, <?php echo $this->Html->link('create a new document', '/dash?template=paper', array('class' => 'link')); ?></p>
		<p>If you created documents before you signed up, just visit them again, and they'll show up on this page.</p>
	<?php endif; ?>
</div>
