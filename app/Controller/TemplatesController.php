<?php
App::uses('AppController', 'Controller');

class TemplatesController extends AppController {

	public $uses = array('Template', 'Project');
	
	public function templates(){
		$templates = $this->Template->find('all');
		$this->set('templates', $templates);
	}
	
	public function usetemplate(){
		$this->autoRender = false;
		
		$template = $this->Template->findByName($_REQUEST['tpl']);
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
			/* insert into database */
			$this->Project->save(array('Project' => array('user_id' => $this->Auth->user('id'), 'projectid' => $upid, 'name' => $_REQUEST['tpl'])));
			$this->redirect('/'.$upid);
			
		} else {
			$rootPath = APP.WEBROOT_DIR.DS.'tex'.DS.'anonymous'.DS;
			$aid = $this->getHash($rootPath);
			$this->Session->write('User.aid', $aid);
			
			if(!file_exists($rootPath.$aid))
				mkdir($rootPath.$aid);
				
			// copy file
			$src = APP.WEBROOT_DIR.DS.'tpl'.DS.$template['Template']['id'];
			$dst = $rootPath.$aid;
			$this->recurse_copy($src,$dst);
			$this->redirect('/'.$aid);
		}
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
