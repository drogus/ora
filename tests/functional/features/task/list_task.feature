Feature: List tasks
	As an organization member
	I want to read the list of tasks available
	in order to understand their current status, members count and how I can contribute

Scenario: Successfully getting the list of available tasks without any parameters
    Given that I am authenticated as "mark.rogers@ora.local" 
    And that I want to find a "Task"
	When I request "/task-management/tasks"
	Then the response status code should be 200
	And the response should be JSON
	And the response should have a "tasks" property

Scenario: Successfully getting the list of tasks of a stream
    Given that I am authenticated as "mark.rogers@ora.local" 
    And that I want to find a "Task"
	And that its "streamID" is "00000000-1000-0000-0000-000000000000"
	When I request "/task-management/tasks"
	Then the response status code should be 200
	And the response should be JSON
	And the response should have a "tasks" property

Scenario: Successfully getting a task that the first member evaluated 1500 credits and the second skipped
    Given that I am authenticated as "mark.rogers@ora.local" 
    And that I want to find a "Task"
	When I request "/task-management/tasks/00000000-0000-0000-0000-000000000107"
	Then the response status code should be 200
	And the response should be JSON
	And the "estimation" property should be "1500"
	And the "members" property size should be "2"

Scenario: Successfully getting a task with skipped estimation by the only member 
    Given that I am authenticated as "mark.rogers@ora.local" 
    And that I want to find a "Task"
	When I request "/task-management/tasks/00000000-0000-0000-0000-000000000106"
	Then the response status code should be 200
	And the response should be JSON
	And the "estimation" property should be "-1"
	And the "members" property size should be "1"

Scenario: Successfully getting a task estimated by only one member 
    Given that I am authenticated as "mark.rogers@ora.local" 
    And that I want to find a "Task"
	When I request "/task-management/tasks/00000000-0000-0000-0000-000000000108"
	Then the response status code should be 200
	And the response should be JSON
	And the response shouldn't have a "estimation" property
	And the "members" property size should be "2"

Scenario: Successfully getting command list on an ongoing tasks of a stream
    Given that I am authenticated as "mark.rogers@ora.local" 
    And that I want to find a "Task"
	When I request "/task-management/tasks/00000000-0000-0000-0000-000000000004"
	And the response status code should be 200
	Then the response should be JSON	
	And the response should have a "_links" property
	And the "_links" property contains "self" key
	And the "_links" property contains "ora:complete" key
	And the "_links" property contains "ora:delete" key
	And the "_links" property contains "ora:estimate" key
	And the "_links" property contains "ora:edit" key

Scenario: Successfully getting command list on a completed tasks of a stream
    Given that I am authenticated as "mark.rogers@ora.local" 
    And that I want to find a "Task"
	When I request "/task-management/tasks/00000000-0000-0000-0000-000000000001"
	Then the response status code should be 200
	And the response should be JSON
	And the response should have a "_links" property
	And the "_links" property contains "self" key
	And the "_links" property contains "ora:estimate" key
	And the "_links" property contains "ora:execute" key

Scenario: Successfully getting command list on an accepted tasks of a stream
    Given that I am authenticated as "mark.rogers@ora.local" 
    And that I want to find a "Task"
	When I request "/task-management/tasks/00000000-0000-0000-0000-000000000002"
	Then the response status code should be 200
	And the response should be JSON
	And the response should have a "_links" property
	And the "_links" property contains "self" key
	And the "_links" property contains "ora:assignShares" key
	And the "_links" property contains "ora:complete" key