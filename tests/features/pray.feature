@api @kyrandia @pray
Feature: Pray commands

  @silveraltar
  Scenario: Praying at the silver altar
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 24    | sword           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 24    | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 4                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 1                   |

    When Joe performs "pray"
    Then Joe should see
    """
    ...As you pray, a vision of the Goddess Tashanna appears before you, shining
    in her eternal, radiant beauty.  She smiles at you and says: "Oh, brave and
    courageous one, there is so much you must learn.  What you have seen is only
    a small fraction of the world of Kyrandia... don't let your pride outmatch
    your knowledge.  Search for the truth of the four elements of all life, and
    know your corresponding birthstones, and their relation to the forces of
    nature and magic.  I bid thee the best of luck."  The goddess then vanishes
    as mysteriously as she had appeared.

    """
    And Flo should see "***\nJoe is praying to the Goddess Tashanna.\n"

  @temple
  Scenario: Praying at the silver altar
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 7     | sword           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 7     | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 4                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 1                   |

    When Joe performs "pray"
    Then Joe should see
    """
    ...As you pray, a vision of the Goddess Tashanna appears in your mind,
    standing before you in a holy brilliance of light.  She smiles and speaks
    softly to you: "One of your many quests must be the realization of your
    astral origins; seek thy birthstones and prove thy knowledge."

    """
    And Flo should see "***\nJoe is praying to the Goddess Tashanna.\n"

  @rock
  Scenario: Praying at the strange large rock
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 27    | sword           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 27    | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 4                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 1                   |
    And the current rock pray count is set to 0

    When Joe performs "pray"
    Then Joe should see "Your prayers are heard."
    And Joe should see "***\nThe mists around the rock begin to swirl magically!\n"
    And Flo should see "***\nThe mists around the rock begin to swirl magically!\n"
    And the current rock pray count should be 1


  @anywhere
  Scenario: Praying anywhere else
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 127   | sword           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 127   | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 4                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 1                   |
    And the current rock pray count is set to 0

    When Joe performs "pray"
    Then Joe should see "...As you pray, a vision of the Goddess Tashanna appears in your mind,\nshe smiles at you, and offers her blessings.\n"
    And Flo should see "***\nJoe is praying piously.\n"
