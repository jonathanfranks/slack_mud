@api @slackmud
Feature: Say command

  @say
  Scenario: Player says nothing
    Given player content:
      | title | field_game | field_location | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 38    | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 38    | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 100   | 1            | Moe                | Moe                   |
    When Joe performs "say"
    Then Joe should see "...Huh?\n"
    And Flo should see "***\nJoe is opening his mouth speechlessly.\n"

  @say
  Scenario: Player says something
    Given player content:
      | title | field_game | field_location | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 0     | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 0     | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 1     | 1            | Moe                | Moe                   |
      | Bo    | kyrandia   | Location 7     | 1            | Bo                 | Bo                    |
    When Joe performs "say Hello!"
    Then Joe should see "...You said it!\n"
    And Flo should see "***\nJoe says: Hello!\n"
    And Moe should see "***\nYou hear a voice nearby.\n"
    And Bo should not see "***\nYou hear a voice nearby.\n"
