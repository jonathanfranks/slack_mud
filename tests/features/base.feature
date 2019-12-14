@api @registration @login
Feature: User registration

  @ted-53
  Scenario: Email registration present
    Given I am an anonymous user
    When I am on the homepage
    And I click "Log in"
