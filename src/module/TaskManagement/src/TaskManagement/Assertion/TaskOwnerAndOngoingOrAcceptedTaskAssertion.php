<?php

namespace TaskManagement\Assertion;


use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Ora\User\User;
use Ora\IllegalStateException;
use Ora\InvalidArgumentException;
use Ora\ReadModel\Task;

class TaskOwnerAndOngoingOrAcceptedTaskAssertion implements AssertionInterface
{
    private $loggedUser;
    
	public function setLoggedUser($loggedUser = null) {
    	$this->loggedUser = $loggedUser;
    }
    
	public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null){
		if(in_array($resource->getStatus(), array(Task::STATUS_ONGOING, Task::STATUS_ACCEPTED))) {

			if($this->loggedUser instanceof User){

				if($resource->hasMember($this->loggedUser)){
					
					$roleMember = $resource->getMemberRole($this->loggedUser->getId());
					if($roleMember == Task::ROLE_OWNER){
						return true;
					}
				}	
    		}
		}
		return false;    	
    }    
}