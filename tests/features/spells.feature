@api @spells
Feature: Spells

  @learn
  Scenario: Learn spell
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 181   | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 181   | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook          |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | smokey,weewillo,tiltowait, zapher |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 2                    | 101                 |
    And Joe should not have weewillo memorized
    When Joe performs "learn weewillo"
    And Joe should have weewillo memorized
    And Joe should see "...You master the weewillo spell!\n"
    And Flo should see "***\nJoe has just memorized a spell from his spellbook!\n"

    # Learn up to the max of 10 to knock weewillo off.
    # 2
    When Joe performs "learn zapher"
    And Joe should see "...You master the zapher spell!\n"
    And Flo should see "***\nJoe has just memorized a spell from his spellbook!\n"

    # 3
    When Joe performs "learn zapher"
    And Joe should see "...You master the zapher spell!\n"
    And Flo should see "***\nJoe has just memorized a spell from his spellbook!\n"

    # 4
    When Joe performs "learn zapher"
    And Joe should see "...You master the zapher spell!\n"
    And Flo should see "***\nJoe has just memorized a spell from his spellbook!\n"

    # 5
    When Joe performs "learn zapher"
    And Joe should see "...You master the zapher spell!\n"
    And Flo should see "***\nJoe has just memorized a spell from his spellbook!\n"

    # 6
    When Joe performs "learn zapher"
    And Joe should see "...You master the zapher spell!\n"
    And Flo should see "***\nJoe has just memorized a spell from his spellbook!\n"

    # 7
    When Joe performs "learn zapher"
    And Joe should see "...You master the zapher spell!\n"
    And Flo should see "***\nJoe has just memorized a spell from his spellbook!\n"

    # 8
    When Joe performs "learn zapher"
    And Joe should see "...You master the zapher spell!\n"
    And Flo should see "***\nJoe has just memorized a spell from his spellbook!\n"

    # 9
    When Joe performs "learn zapher"
    And Joe should see "...You master the zapher spell!\n"
    And Flo should see "***\nJoe has just memorized a spell from his spellbook!\n"

    # 10
    When Joe performs "learn zapher"
    And Joe should see "...You master the zapher spell!\n"
    And Flo should see "***\nJoe has just memorized a spell from his spellbook!\n"

    # Too full! Lose the first spell.
    When Joe performs "learn zapher"
    And Joe should see "...You master the zapher spell, however the weewillo spell slips from your memory!\n"
    And Flo should see "***\nJoe has just memorized a spell from his spellbook!\n"

