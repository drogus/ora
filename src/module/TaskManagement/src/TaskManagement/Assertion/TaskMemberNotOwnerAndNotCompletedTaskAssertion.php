<?php

namespace TaskManagement\Assertion;


use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;
use Ora\User\User;
use Ora\ReadModel\Task;

class TaskMemberNotOwnerAndNotCompletedTaskAssertion extends NotCompletedTaskAssertion
{
    private $loggedUser;
    
	public function setLoggedUser($loggedUser = null) {
    	$this->loggedUser = $loggedUser;
    }
    
	public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null){
		
		if(parent::assert($acl, $role, $resource, $privilege)){
			
			if($this->loggedUser instanceof User){

				if($resource->hasMember($this->loggedUser)){

					$roleMember = $resource->getMemberRole($this->loggedUser);
					if($roleMember != Task::ROLE_OWNER){
						return true;
					}		
				}
			}
		}
		
		return false;
    }    
}