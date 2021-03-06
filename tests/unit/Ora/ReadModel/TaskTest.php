<?php
namespace Ora\ReadModel;

use Ora\User\User;

class TaskTest extends \PHPUnit_Framework_TestCase {
	
	protected function setUp() {
		
	}

	public function testUpdateMembersShare()
	{	
		$user1 = User::create();
		$user2 = User::create();
		$user3 = User::create();
		
		$datetime = new \DateTime();
		$task = new Task('1');
		$task->addMember($user1, Task::ROLE_OWNER, $user1, $datetime)
			 ->addMember($user2, Task::ROLE_MEMBER, $user2, $datetime)
			 ->addMember($user3, Task::ROLE_MEMBER, $user3, $datetime);
		$member1 = $task->getMember($user1);
		$member2 = $task->getMember($user2);
		$member3 = $task->getMember($user3);
		
		$member1->assignShare($member1, 0.34, $datetime);
		$member1->assignShare($member2, 0.26, $datetime);
		$member1->assignShare($member3, 0.4, $datetime);
		
		$member2->assignShare($member1, 0.62, $datetime);
		$member2->assignShare($member2, 0.13, $datetime);
		$member2->assignShare($member3, 0.25, $datetime);
				
		$member3->assignShare($member1, 0.44, $datetime);
		$member3->assignShare($member2, 0.21, $datetime);
		$member3->assignShare($member3, 0.35, $datetime);
				
		$this->assertEquals(0.4667, $member1->getShare());
		$this->assertEquals(0.2, $member2->getShare());
		$this->assertEquals(0.3333, $member3->getShare());
	}

	public function testUpdateMembersShareWith0()
	{	
		$user1 = User::create();
		$user2 = User::create();
		$user3 = User::create();
		
		$datetime = new \DateTime();
		$task = new Task('1');
		$task->addMember($user1, Task::ROLE_OWNER, $user1, $datetime)
			 ->addMember($user2, Task::ROLE_MEMBER, $user2, $datetime)
			 ->addMember($user3, Task::ROLE_MEMBER, $user3, $datetime);
		$member1 = $task->getMember($user1);
		$member2 = $task->getMember($user2);
		$member3 = $task->getMember($user3);
		
		$member1->assignShare($member1, 34, $datetime);
		$member1->assignShare($member2, 66, $datetime);
		$member1->assignShare($member3, 0, $datetime);
		
		$member2->assignShare($member1, 27, $datetime);
		$member2->assignShare($member2, 73, $datetime);
		$member2->assignShare($member3, 0, $datetime);
				
		$member3->assignShare($member1, 50, $datetime);
		$member3->assignShare($member2, 50, $datetime);
		$member3->assignShare($member3, 0, $datetime);
				
		$this->assertEquals(37, $member1->getShare());
		$this->assertEquals(63, $member2->getShare());
		$this->assertEquals(0, $member3->getShare());
	}

	public function testUpdateMembersShareWithSkipAnd0()
	{	
		$user1 = User::create();
		$user2 = User::create();
		$user3 = User::create();
		
		$datetime = new \DateTime();
		$task = new Task('1');
		$task->addMember($user1, Task::ROLE_OWNER, $user1, $datetime)
			 ->addMember($user2, Task::ROLE_MEMBER, $user2, $datetime)
			 ->addMember($user3, Task::ROLE_MEMBER, $user3, $datetime);
		$member1 = $task->getMember($user1);
		$member2 = $task->getMember($user2);
		$member3 = $task->getMember($user3);
		
		$member1->assignShare($member1, null, $datetime);
		$member1->assignShare($member2, null, $datetime);
		$member1->assignShare($member3, null, $datetime);
		
		$member2->assignShare($member1, 27, $datetime);
		$member2->assignShare($member2, 73, $datetime);
		$member2->assignShare($member3, 0, $datetime);
				
		$member3->assignShare($member1, 50, $datetime);
		$member3->assignShare($member2, 50, $datetime);
		$member3->assignShare($member3, 0, $datetime);
				
		$this->assertEquals(38.5, $member1->getShare());
		$this->assertEquals(61.5, $member2->getShare());
		$this->assertEquals(0, $member3->getShare());
	}

	public function testUpdateMembersShareWithSkip()
	{	
		$user1 = User::create();
		$user2 = User::create();
		$user3 = User::create();
		
		$datetime = new \DateTime();
		$task = new Task('1');
		$task->addMember($user1, Task::ROLE_OWNER, $user1, $datetime)
			 ->addMember($user2, Task::ROLE_MEMBER, $user2, $datetime)
			 ->addMember($user3, Task::ROLE_MEMBER, $user3, $datetime);
		$member1 = $task->getMember($user1);
		$member2 = $task->getMember($user2);
		$member3 = $task->getMember($user3);
		
		$member1->assignShare($member1, 0.34, $datetime);
		$member1->assignShare($member2, 0.26, $datetime);
		$member1->assignShare($member3, 0.4, $datetime);
		
		$member2->assignShare($member1, null, $datetime);
		$member2->assignShare($member2, null, $datetime);
		$member2->assignShare($member3, null, $datetime);
				
		$member3->assignShare($member1, 0.44, $datetime);
		$member3->assignShare($member2, 0.21, $datetime);
		$member3->assignShare($member3, 0.35, $datetime);
				
		$this->assertEquals(0.39, $member1->getShare());
		$this->assertEquals(0.235, $member2->getShare());
		$this->assertEquals(0.375, $member3->getShare());
	}

	public function testUpdateMembersShareWithAllSkip()
	{	
		$user1 = User::create();
		$user2 = User::create();
		$user3 = User::create();
		
		$datetime = new \DateTime();
		$task = new Task('1');
		$task->addMember($user1, Task::ROLE_OWNER, $user1, $datetime)
			 ->addMember($user2, Task::ROLE_MEMBER, $user2, $datetime)
			 ->addMember($user3, Task::ROLE_MEMBER, $user3, $datetime);
		$member1 = $task->getMember($user1);
		$member2 = $task->getMember($user2);
		$member3 = $task->getMember($user3);
		
		$member1->assignShare($member1, null, $datetime);
		$member1->assignShare($member2, null, $datetime);
		$member1->assignShare($member3, null, $datetime);
		
		$member2->assignShare($member1, null, $datetime);
		$member2->assignShare($member2, null, $datetime);
		$member2->assignShare($member3, null, $datetime);
				
		$member3->assignShare($member1, null, $datetime);
		$member3->assignShare($member2, null, $datetime);
		$member3->assignShare($member3, null, $datetime);
				
		$this->assertNull($member1->getShare());
		$this->assertNull($member2->getShare());
		$this->assertNull($member3->getShare());
	}
}