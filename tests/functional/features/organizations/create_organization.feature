Feature: Create organization
	As a user
	I want to create a new organization
	in order to have a new group of people that can work on organization related streams

Scenario: Cannot create an organization anonymously
	Given that I want to make a new "Organization"
	And that its "subject" is "My First Organization"
	When I request "/user/organizations"
	Then the response status code should be 401

Scenario: Successfully creating an organization
	Given that I am authenticated as "mark.rogers@ora.local"
	And that I want to make a new "Organization"
	And that its "name" is "My First Organization"
	When I request "/user/organizations"
	Then the response status code should be 201
	And the header "Location" should be "/user/organizations/[0-9a-z\-]+"

Scenario: Successfully creating an organization without a name
	Given that I am authenticated as "mark.rogers@ora.local"
	And that I want to make a new "Organization"
	When I request "/user/organizations"
	Then the response status code should be 201
	And the header "Location" should be "/user/organizations/[0-9a-z\-]+"