<!-- <div class="row">
	<div class='add-file'>
		<span>add</span>
	</div>
	<div class="list_files">
		<ul class="list-inline">
			<?php 
				if($resources){
					foreach($resources as $r){
						echo "<li>".$r.$this->Html->link('Edit', '/?fn='.base64_encode($r), array('class' => 'link'))."</li>";
					}
				}
			?>
		</ul>
	</div>
</div> -->
<!--<?php if(isset($output) && isset($output['errors'])): ?>
<div class="errors">errors<span class='error-count'><?php echo count($output['errors']); ?></span>
	<div class="error-list">
		<ul>
		<?php foreach($output['errors'] as $error): ?>
			<li><?php echo $error; ?></li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php endif; ?>
<?php if(isset($output) && isset($output['warnings'])): ?>
<div class="warnings">Warnings<span class='warning-count'><?php echo count($output['warnings']); ?></span>
	<div class="warning-list">
		<ul>
		<?php foreach($output['warnings'] as $warning): ?>
			<li><?php echo $warning; ?></li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php endif; ?> -->
<div class="no-position col-lg-6 col-md-6 col-sm-6">
	<?php
	
		echo $this->Form->create('Code', array('url' => '/'.$sessionId));
			echo $this->Form->hidden('data-checksum', array('value' => md5($filedata['data']), 'class' => 'data-checksum'));
			echo $this->Form->hidden('current-file-name', array('value' => $filedata['filename'], 'class' => 'current-file-name'));
			echo $this->Form->input('code', array('id' => 'code', 'label' => false, 'value' => $filedata['data'], 'type' => 'textarea'));
		echo $this->Form->end(array('label' => 'Compile', 'class' => 'btn btn-primary btn-sm code-compile')); ?>
		
</div>
<div class="col-lg-6 col-md-6 col-sm-6 code-output">
	<?php echo $this->element('show_pdf'); ?>
</div>

<!-- <iframe src="/latexnew/app/View/Pages/pdfjs/index.php"></iframe> -->
