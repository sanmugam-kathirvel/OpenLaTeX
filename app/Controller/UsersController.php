<?php
class UsersController extends AppController {
	public $components = array('ExtAuth.ExtAuth', 'Auth', 'Session');
	
	public function auth_login($provider) {
	    $result = $this->ExtAuth->login($provider);
	    if ($result['success']) {
	
	        $this->redirect($result['redirectURL']);
	
	    } else {
	        $this->Session->setFlash($result['message']);
	        $this->redirect($this->Auth->loginAction);
	    }
	}
	
	public function auth_callback($provider=NULL) {
	    $result = $this->ExtAuth->loginCallback($provider);
	    if ($result['success']) {
	
	        $this->__successfulExtAuth($result['profile'], $result['accessToken']);
	
	    } else {
	        $this->Session->setFlash($result['message']);
	        $this->redirect($this->Auth->loginAction);
	    }
	}


    public function beforeFilter() {
	    parent::beforeFilter();
	    $this->Auth->allow('add'); // Letting users register themselves
	}
	
	public function login() {
		//var_dump($this->Session->delete('User'));
		//var_dump($this->Session->read('User'));
	    if ($this->request->is('post')) {
	        if ($this->Auth->login()) {
	        	$this->Session->write('User.uid', $this->Auth->user('id'));
	        	$this->Session->delete('User.sessionId.aid');
	            return $this->redirect('/dash');
	        }
	        $this->Session->setFlash(__('Invalid username or password, try again'));
	    }
	}
	
	public function logout() {
		/* delete add session relates to user */
		$this->Session->delete('User');
		if($this->Auth->logout())
			$this->redirect('/');
	    #return $this->redirect($this->Auth->logout());
	}

    public function index() {
        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
    }

    public function view($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('user', $this->User->read(null, $id));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
        }
    }

    public function edit($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
        } else {
            $this->request->data = $this->User->read(null, $id);
            unset($this->request->data['User']['password']);
        }
    }

    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->User->delete()) {
            $this->Session->setFlash(__('User deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
	private function __successfulExtAuth($incomingProfile, $accessToken) {
	
	    // search for profile
	    $this->SocialProfile->recursive = -1;
	    $existingProfile = $this->SocialProfile->find('first', array(
	        'conditions' => array('oid' => $incomingProfile['oid'])
	    ));
	
	    if ($existingProfile) {
	
	        // Existing profile? log the associated user in.
	        $user = $this->User->find('first', array(
	            'conditions' => array('id' => $existingProfile['SocialProfile']['user_id'])
	        ));
	
	        $this->__doAuthLogin($user);
	    } else {
	
	        // New profile.
	        if ($this->Auth->loggedIn()) {
	
	            // user logged in already, attach profile to logged in user.
	
	            // create social profile linked to current user
	            $incomingProfile['user_id'] = $this->Auth->user('id');
	            $this->SocialProfile->save($incomingProfile);
	            $this->Session->setFlash('Your ' . $incomingProfile['provider'] . ' account has been linked.');
	            $this->redirect($this->Auth->loginRedirect);
	
	        } else {
	
	            // no-one logged in, must be a registration.
	            unset($incomingProfile['id']);
	            $user = $this->User->register(array('User' => $incomingProfile));
	
	            // create social profile linked to new user
	            $incomingProfile['user_id'] = $user['User']['id'];
	            $incomingProfile['last_login'] = date('Y-m-d h:i:s');
	            $incomingProfile['access_token'] = serialize($accessToken);
	            $this->SocialProfile->save($incomingProfile);
	
	            // populate user detail fields that can be extracted
	            // from social profile
	            $profileData = array_intersect_key(
	                $incomingProfile,
	                array_flip(array(
	                    'email',
	                    'given_name',
	                    'family_name',
	                    'picture',
	                    'gender',
	                    'locale',
	                    'birthday',
	                    'raw'
	                ))
	            );
	
	            $this->User->setupDetail();
	            $this->User->UserDetail->saveSection(
	                $user['User']['id'],
	                array('UserDetail' => $profileData),
	                'User'
	            );
	
	            // log in
	            $this->__doAuthLogin($user);
	        }
	    }
	}
	
	private function __doAuthLogin($user) {
	    if ($this->Auth->login($user['User'])) {
	        $user['last_login'] = date('Y-m-d H:i:s');
	        $this->User->save(array('User' => $user));
	
	        $this->Session->setFlash(sprintf(__d('users', '%s you have successfully logged in'), $this->Auth->user('username')));
	        $this->redirect($this->Auth->loginRedirect);
	    }
	}


}





?>
