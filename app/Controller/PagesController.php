<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
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
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

/**
 * Displays a view
 *
 * @param mixed What page to display
 * @return void
 * @throws NotFoundException When the view file could not be found
 *	or MissingViewException in debug mode.
 */
	public function display() {
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));

		try {
			$this->render(implode('/', $path));
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
	}
	
	public function home() {
		
	}
	public function index() {
		
		/* compile */
		if($this->request->data && isset($this->request->data['Code']['current-file-name']) && isset($this->request->data['Code']['code'])) {
			$pathSessionData = $this->setSessionPath();
			$rootPath = $pathSessionData['rootPath'];
			$sessionId = $pathSessionData['sessionId'];
		
			$filename = $this->request->data['Code']['current-file-name'];
			$code = $this->request->data['Code']['code'];
		
			if(!file_exists($rootPath))
				mkdir($rootPath, 0777);
			if(file_exists($rootPath.'main.pdf'))
				unlink($rootPath.'main.pdf');
			$handle = fopen($rootPath.$filename, 'w');
			fwrite($handle, $code);
			fclose($handle);
			$currentDir = getcwd();
			chdir($rootPath);
			exec('pdflatex main.tex');
			chdir($currentDir);
			$output = array();
			$handle = fopen($rootPath."main.log", 'r');
			$count = -1;
			$count1 = -1;
			$warningFlag = 0;
			while(!feof($handle)) {
				$compileLog = fgets($handle);
				if(!(stripos($compileLog, "warning:") === false)) {
					$count++;
					$output["warnings"][$count] = $compileLog;
					$warningFlag = 1;
				}elseif(!(stripos($compileLog, "error") === false)) {
					$output["errors"][++$count1] = $compileLog;
				}elseif($warningFlag) {
					$output["warnings"][$count] .= $compileLog;
					if($warningFlag == 2) {
						$warningFlag = 0;
						$output["warnings"][$count] .= '...';
					}else {
						$warningFlag++;
					}
				}
			}
			fclose($handle);
			if(file_exists($rootPath.'main.aux'))
				unlink($rootPath.'main.aux');
			/*if(file_exists($rootPath.'main.log'))
				unlink($rootPath.'main.log');*/
			if(file_exists($rootPath.'main.out'))
				unlink($rootPath.'main.out');
			if(file_exists($rootPath.'main.toc'))
				unlink($rootPath.'main.toc');
			/*if(file_exists($rootPath.'main.tex'))
				unlink($rootPath.'main.tex');*/
			if(file_exists($rootPath.'main.pdf')){
				$output['success'] = 1;
			}else {
				$output['success'] = 0;
			}
		
			$this->set('output', $output);
			
		}
		
		/* compile end */
			
		$this->set('resources', $this->resourcesList());
		$pathSessionData = $this->setSessionPath();
		$rootPath = $pathSessionData['rootPath'];
		//var_dump($rootPath);
		if($this->Auth->user('id'))
			$sessionId = $pathSessionData['sessionId']['upid'];
		else
			$sessionId = $this->Session->read('User.aid');
		
		$this->set('sessionId', $sessionId);
		$fileName = 'main.tex';
		
		/* read the resources */
		if($this->Auth->user('id')){
			$handle = opendir($rootPath);
			$file_types = array('image/jpeg', 'text/x-tex', 'image/png');
			$files = array();
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".." && in_array(mime_content_type($rootPath.$entry), $file_types)) {
					$files[] = $entry;
				}
			}
			//var_dump($files);
			$this->set('files', $files);
		}
		/* read the resources end */
		
		$data = '';
		$pdfpath = '';
		if(file_exists($rootPath)){
			if(!file_exists($rootPath.$fileName))
				fopen($rootPath.$fileName, 'w');
			$handle = fopen($rootPath.$fileName, 'r');
			if(filesize($rootPath.$fileName) > 0)
				$data = fread($handle,filesize($rootPath.$fileName));
			fclose($handle);
		
			if($this->Auth->user('id') && file_exists($rootPath.'main.pdf'))
				$pdfpath = FULL_BASE_URL.DS.$this->request->base.DS.'tex'.DS.'users'.DS.$this->Auth->user('id').DS.$sessionId.DS.'main.pdf';
			if(!$this->Auth->user('id') && file_exists($rootPath.'main.pdf'))
				$pdfpath = FULL_BASE_URL.$this->request->base.DS.'tex'.DS.'anonymous'.DS.$sessionId.DS.'main.pdf';
			$this->set('filedata', array('data' => $data, 'filename' => $fileName, 'pdfpath' => $pdfpath));
		}else{
			$this->Session->delete('User');
			$this->redirect('/');
		}
		
	}
	
	public function compile() {
		
	}
	public function anoysdashboard(){
		$this->autoRender = false;
		$rootPath = APP.WEBROOT_DIR.DS.'tex'.DS.'anonymous/';
		
		/* open existing project */
		if(isset($this->request->query['oldtemplate']) && $this->request->query['oldtemplate'] == 'open' && isset($this->request->query['p'])){
			if(file_exists($rootPath.base64_decode($this->request->query['p']))){
				$this->Session->write('User.id', base64_decode($this->request->query['p']));
				return $this->redirect('/'.base64_decode($this->request->query['p']));
			}
		}
		
		if(isset($this->request->query['template']) && $this->request->query['template'] == 'paper' && !$this->Session->read('User.aid')){
			jmp1:
				$aid = uniqid();
			$handle = opendir($rootPath);
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".." && $entry == $aid) {
					goto jmp1;
				}
			}
			$this->Session->write('User.aid', $aid);
			

			if(!file_exists($rootPath.$aid))
				mkdir($rootPath.$aid);
			if(!file_exists($rootPath.$aid.DS.'main.tex')){
				$handle = fopen($rootPath.$aid.DS.'main.tex', 'w');
				fclose($handle);
				/* TODO:read default template on to file*/
			}
			return $this->redirect('/'.$aid);
		}else{
			return $this->redirect('/'.$this->Session->read('User.aid'));
		}
	}
	public function dashboard() {
		/* create new project */
		
		if(isset($this->request->query['template']) && $this->request->query['template'] == 'paper'){
		
			$pathSessionData = $this->setSessionPath('userpath');
			$rootPath = $pathSessionData['rootPath'];
			/* create user filder */
			if(!file_exists($rootPath))
				mkdir($rootPath);
			
			jmp1:
				$upid = uniqid();
			$handle = opendir('tex/users/'.$this->Auth->user('id'));
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".." && $entry == $upid) {
					goto jmp1;
				}
			}
			$this->Session->write('User.upid', $upid);
			/* create user project folder */
			if(!file_exists($rootPath.$upid))
				mkdir($rootPath.$upid);
			if(!file_exists($rootPath.$upid.DS.'main.tex')){
				$handle = fopen($rootPath.$upid.DS.'main.tex', 'w');
				fclose($handle);
				/* TODO:read default template on to file*/
			}
			return $this->redirect('/'.$upid);
		}
		
		/* open existing project */
		if(isset($this->request->query['oldtemplate']) && $this->request->query['oldtemplate'] == 'open' && isset($this->request->query['p'])){
			if(file_exists(getcwd()."/tex/users/".$this->Auth->user('id').DS.base64_decode($this->request->query['p']))){
				$this->Session->write('User.upid', base64_decode($this->request->query['p']));
				return $this->redirect('/'.base64_decode($this->request->query['p']));
			}
		}
		
		/* list all user project */
		$this->set('dirlist', $this->listDir());
		
	}
	public function autoSave(){
		$this->autoRender = false;
		
		if(isset($_POST['filename']) && isset($_POST['filedata'])){
			$pathSessionData = $this->setSessionPath();
			//var_dump($_POST['filename']);
			$handle = fopen($pathSessionData['rootPath'].$_POST['filename'], 'w');
			if(fwrite($handle, base64_decode($_POST['filedata'])))
				$output['status'] = true;
			else
				$output['status'] = false;
			fclose($handle);
		}else{
			$output['status'] = false;
		}
		echo json_encode($output);
		exit;
	}
	public function setSessionPath($userpath = NULL){
		$sessionId = $this->Session->read('User');
		$rootPath = APP.WEBROOT_DIR.DS.'tex'.DS;
		if($this->Auth->user('id')){
			if($userpath == 'userpath')
				$rootPath = APP.WEBROOT_DIR.DS.'tex'.DS.'users'.DS.$sessionId['uid'].DS;
			elseif(isset($sessionId['uid']) && $sessionId['uid'] != '' && isset($sessionId['upid']))
				$rootPath = APP.WEBROOT_DIR.DS.'tex'.DS.'users'.DS.$sessionId['uid'].DS.$sessionId['upid'].DS;
		}elseif(isset($sessionId['aid']) && $sessionId['aid'] != ''){
			$rootPath = APP.WEBROOT_DIR.DS.'tex'.DS.'anonymous'.DS.$sessionId['aid'].DS;
		}else{
			// redirect and display error.base64_decode($this->request->query['p']))){
			
		}
		
		return array('rootPath' => $rootPath, 'sessionId' => $sessionId);
	}
	
	public function listDir(){
		$pathSessionData = $this->setSessionPath('userpath');
		$rootPath = $pathSessionData['rootPath'];
		if($this->Auth->user('id')){
			if (file_exists($rootPath) && $handle = opendir($rootPath)) {
				$dirlist = array();
				while (false !== ($entry = readdir($handle))) {
					if($entry != '.' && $entry != '..' && is_dir($rootPath.$entry))
						$dirlist[] = $entry;
				}
				return $dirlist;
			}
		}
	}
	
	public function resources(){

		//$this->autoRender = false;
		$sessionId = $this->Session->read('User');
		$pathSessionData = $this->setSessionPath();
		$rootPath = $pathSessionData['rootPath'];
		$sessionId = $pathSessionData['sessionId'];
		if(isset($this->request->data['Pages']['resources']['size']) && $this->request->data['Pages']['resources']['size'] > 0 && $sessionId){
			if(!file_exists($rootPath))
				mkdir($rootPath, 0777);
			
			if(move_uploaded_file($this->request->data['Pages']['resources']['tmp_name'], $rootPath.$this->request->data['Pages']['resources']['name'])){
				$this->redirect($this->referer());
			}else{
				$this->redirect($this->referer());
			}
		}
	}
	
	public function resourcesList(){
		//$this->autoRender = false;
		$pathSessionData = $this->setSessionPath();
		$rootPath = $pathSessionData['rootPath'];
		if (file_exists($rootPath) && $handle = opendir($rootPath)) {
			$file_list = array();
			while (false !== ($entry = readdir($handle))) {
				if($entry != '.' && $entry != '..' && $entry != 'main.log' && $entry != 'main.pdf' && !is_dir($rootPath.$entry))
					$file_list[] = $entry;
			}
			if(isset($_POST['type']) && $_POST['type'] == 'json'){
				$this->autoRender = false;
				return json_encode($file_list);
				exit;
			}else{
				return $file_list;
			}
		}
	}
	
	public function delete(){
		$this->autoRender = false;
		$pathSessionData = $this->setSessionPath('userpath');
		$rootPath = $pathSessionData['rootPath'];
		if($_REQUEST['p']){
			$this->rrmdir($rootPath.base64_decode($_REQUEST['p']));
			$this->redirect('/dash');
		}else{
			$this->redirect('/dash');
		}
	}
	
	function rrmdir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
						if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}
 
	public function addFile(){
		
		/* create file */
		$pathSessionData = $this->setSessionPath();
		$rootPath = $pathSessionData['rootPath'];
		$sessionId = $pathSessionData['sessionId'];		
		$fileName = strtolower($this->request->data['filename']);
		if(!file_exists($rootPath.$fileName)){
			if(fopen($rootPath.$fileName, 'w'))
				$output['status'] = true;
			else
				$output['status'] = false; 
		}else{
			$output['status'] = false; 
		}
		echo json_encode($output);
		exit;
	}

}
