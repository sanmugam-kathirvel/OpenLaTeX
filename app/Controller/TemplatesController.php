<?php
App::uses('AppController', 'Controller');

class TemplatesController extends AppController {

	#public $uses = array();
	
	public function templates(){
		$templates = $this->Template->find('all');
		$this->set('templates', $templates);
	}
	
	public function usetemplate(){
		$this->autoRender = false;
		
		$template = $this->Template->findByName($_REQUEST['tpl']);
		//var_dump($template);
		
		//path 
		if($this->Auth->user('id')){
			$rootPath = APP.WEBROOT_DIR.DS.'tex'.DS.'users'.DS.$this->Auth->user('id').DS;
			$upid = $this->getHash($rootPath);
			$this->Session->write('User.upid', $upid);
			if(!file_exists($rootPath.$upid))
				mkdir($rootPath.$upid);
				
			// copy file
			$src = APP.WEBROOT_DIR.DS.'tpl'.DS.$template['Template']['id'];
			$dst = $rootPath.$upid;
			$this->recurse_copy($src,$dst);
			$this->redirect('/'.$upid);
			
		} else {
			$rootPath = APP.WEBROOT_DIR.DS.'tex'.DS.'anonymous'.DS;
			$aid = $this->getHash($rootPath);
			$this->Session->write('User.aid', $aid);
		}
		
		die;
		
	}
	
	public function getHash($rootPath){
		jmp1:
			$hash = uniqid();
		$handle = opendir($rootPath);
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != ".." && $entry == $hash) {
				goto jmp1;
			}
		}
		return $hash;
	}
	
	public function recurse_copy($src,$dst) { 
    $dir = opendir($src); 
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
            } 
            else { 
                copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
} 

}