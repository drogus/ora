<?php

namespace TaskManagement\Assertion;


use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;
use Ora\User\User;

class OrganizationMemberNotTaskMemberAndNotCompletedTaskAssertion extends NotCompletedTaskAssertion
{
    private $loggedUser;
    
	public function setLoggedUser($loggedUser = null) {
    	$this->loggedUser = $loggedUser;
    }
    
	public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null){
		
		//TODO: manca sul WriteModel la possibilita' di accedere, dalla risorsa Task, allo Stream associato.
		//      Al momento questa assertion e' usata solamente per il ReadModel
		if(parent::assert($acl, $role, $resource, $privilege)){
			
			if($this->loggedUser instanceof User){
				
				if(!$resource->hasMember($this->loggedUser)){
			    
					return $this->loggedUser->isMemberOf($resource->getStream()->getOrganization());					
			    }
			}
			
		}
		
		return false;		
    }
}