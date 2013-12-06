<?php
class Project extends AppModel {
	public $belongsTo = array('User');
   /* public $validate = array(
    	'projectid' => array(
    		'unique' => array(
				'rule' => array('checkUnique', array('projectid', 'user_id')),
				'message' => 'This project already exits, check your input and try again.',
			)
    	)
    );*/
	
}
?>
