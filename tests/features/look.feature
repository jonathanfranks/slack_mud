@api @registration @login
Feature: Kyrandia commands not specific to locations

  @look @location
  Scenario: Look command
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 0     | garnet,garnet   | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 0     | ruby,diamond    | 1            | Flo                | Flo                   |
    When "Joe" performs "look"
    Then "Joe" should see "...You're near a mystical willow tree. Before you rises the mysterious, eerie silhouette of a willow tree, the most strange and fascinating of all of the natural magic of Kyrandia. Although willows are normally associated with death and sorrow, this particular tree is unusual, as it radiates an extremely powerful aura of good -- a miracle of the hailed Goddess Tashanna. A small, dirt path winds off to the north, and dark forest surrounds you in all other directions."
    And "Flo" should see "Joe is carefully inspecting the surroundings."

  @look @location @brief
  Scenario: Look brief command
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 0     | garnet,garnet   | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 0     | ruby,diamond    | 1            | Flo                | Flo                   |
    When "Joe" performs "look brief"
    Then "Joe" should see "...You're near a mystical willow tree.\n"
    And "Flo" should see "Joe is glancing around briefly!"

  @look @spellbook
  Scenario: Look spellbook command
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 0     | garnet,garnet   | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 0     | ruby,diamond    | 1            | Flo                | Flo                   |
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level |
      | kyrandia_profile_Flo | Flo          | 1                        | 1                    |
    When "Joe" performs "look spellbook"
    Then "Joe" should see "You currently have no spells in your spellbook."
    And "Flo" should not have any messages

  @look @player
  Scenario: Look at player
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 0     | garnet,garnet   | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 0     | ruby,diamond    | 1            | Flo                | Flo                   |
      | Mo    | kyrandia   | Location 0     |                 | 1            | Mo                 | Mo                    |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level |
      | kyrandia_profile_Flo | Flo          | 1                        | 1                    |
    When "Joe" performs "look flo"
    Then "Joe" should see "...Flo is a simple, although rather beautiful, young woman. Something in her eyes fills your soul with peace and comfort. Her smile is perhaps the most powerful magic she possesses, especially since she doesn't even bear a Patch of Sorcery -- a lowly apprentice. She seems to be holding a ruby and a diamond."
    And "Flo" should see "***\nJoe is looking at you carefully.\n"
    And "Mo" should see "***\nJoe is closely examining Flo.\n"

  @look
  Scenario: Look at held item
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 0     | wand,wand   | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 0     | ruby,diamond    | 1            | Flo                | Flo                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level |
      | kyrandia_profile_Flo | Flo          | 1                        | 1                    |
    When "Joe" performs "look wand"
    Then "Joe" should see "...The wand is about two feet in length and about half an inch in diameter. It appears to be constructed from pure silver, although it is much lighter than you would expect. A silver tulip entwined within two hearts has been intricately carved across its length."
    And "Flo" should see "***\nJoe is examining his wand.\n"

  @look @item
  Scenario: Look at item in location
    Given location content:
      | title        | body                    | field_visible_items | field_object_location |
      | Janet's Void | You're in Janet's Void. | dragonstaff,pearl   | in the void           |
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Janet's Void   | garnet,garnet   | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Janet's Void   | ruby,diamond    | 1            | Flo                | Flo                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level |
      | kyrandia_profile_Flo | Flo          | 1                        | 1                    |
    When "Joe" performs "look pearl"
    Then "Joe" should see "...The pearl is a lovely little stone, pure white in color, with a shiny surface that reflects all surrounding light, causing it to sparkle with a special brilliance of its own."
    And "Flo" should see "***\nJoe is examining the pearl in the void.\n"

  @look @invisible
  Scenario: Look at an invisible player
    Given location content:
      | title        | body                    | field_visible_items | field_object_location |
      | Janet's Void | You're in Janet's Void. | dragonstaff,pearl   | in the void           |
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Janet's Void   | garnet,garnet   | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Janet's Void   | ruby,diamond    | 1            | Flo                | Flo                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_invisf |
      | kyrandia_profile_Flo | Flo          | 1                        | 1                    | 1                     |
    When "Joe" performs "look flo"
    Then "Joe" should see "...The force is unseen!\n"
    And "Flo" should see "***\nJoe is looking at you carefully.\n"

  @look @willow
  Scenario: Look at a willowisp player
    Given location content:
      | title        | body                    | field_visible_items | field_object_location |
      | Janet's Void | You're in Janet's Void. | dragonstaff,pearl   | in the void           |
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Janet's Void   | garnet,garnet   | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Janet's Void   | ruby,diamond    | 1            | Flo                | Flo                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_willow |
      | kyrandia_profile_Flo | Flo          | 1                        | 1                    | 1                     |
    When "Joe" performs "look flo"
    Then "Joe" should see "...The willowisp quickly flies from your direct vision.\n"
    And "Flo" should see "***\nJoe is looking at you carefully.\n"

  @look @pegasus
  Scenario: Look at a pegasus player
    Given location content:
      | title        | body                    | field_visible_items | field_object_location |
      | Janet's Void | You're in Janet's Void. | dragonstaff,pearl   | in the void           |
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Janet's Void   | garnet,garnet   | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Janet's Void   | ruby,diamond    | 1            | Flo                | Flo                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_pegasu |
      | kyrandia_profile_Flo | Flo          | 1                        | 1                    | 1                     |
    When "Joe" performs "look flo"
    Then "Joe" should see "...The pegasus is a wonderful creature, but it moves quickly away from your\ndirect vision.\n"
    And "Flo" should see "***\nJoe is looking at you carefully.\n"

  @look @pseudodragon
  Scenario: Look at a pseudodragon player
    Given location content:
      | title        | body                    | field_visible_items | field_object_location |
      | Janet's Void | You're in Janet's Void. | dragonstaff,pearl   | in the void           |
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Janet's Void   | garnet,garnet   | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Janet's Void   | ruby,diamond    | 1            | Flo                | Flo                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_pdragn |
      | kyrandia_profile_Flo | Flo          | 1                        | 1                    | 1                     |
    When "Joe" performs "look flo"
    Then "Joe" should see "...The pseudo dragon is so ugly, that you turn your head immediately.\n"
    And "Flo" should see "***\nJoe is looking at you carefully.\n"
