@api @registration @login
Feature: Kyrandia commands not specific to locations

  @level @kneel @level2
  Scenario: Level 2
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 0     | garnet,garnet   | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 0     | ruby,diamond    | 1            | Flo                | Flo                   |
    And "Joe" should not have the spell "smokey"
    And "Joe" should be level "1"
    When "Joe" performs "kneel"
    Then "Joe" should see '...As you kneel, a vision of the Goddess Tashanna materializes before you.\nShe lays her hand gently upon your shoulder and says to you, "Rise, rise,\nMagic-user!  Your first advancement has begun."  She then vanishes, and\nyou feel yourself grow in power!\n***\nYou are now at level 2!\n***\nA spell has been added to your spellbook!\n'
    And "Flo" should see "***\nJoe seems to suddenly grow in strength and knowledge!\n"
    And "Joe" should have the spell "smokey"
    And "Joe" should be level "2"

  @level @kneel @level3
  Scenario: Level 3
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 7     | garnet,garnet   | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 7     | ruby,diamond    | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    |

    And "Joe" should be level "2"
    When "Joe" performs "glory be to tashanna"
    Then "Joe" should see '...As you praise the Goddess Tashanna, you feel yourself grow in power!\n***\nYou are now at level 3!\n'
    And "Flo" should see "***\nJoe seems to suddenly grow in strength and knowledge!\n"
    And "Joe" should be level "3"
