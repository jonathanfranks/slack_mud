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

  @level @kneel @level4 @silveraltar
  Scenario: Level 4
    Given player content:
      | title | field_game | field_location | field_inventory                                | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 24    | garnet,garnet,pearl,bloodstone,diamond,emerald | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 24    | ruby,diamond                                   | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_birth_stones     |
      | kyrandia_profile_Joe | Joe          | 0                        | 3                    | garnet,pearl,bloodstone,diamond |
    And Joe should be level "3"

    # Offer item player doesn't have.
    When Joe performs "offer key"
    Then Joe should see "...Unfortunately, you don't have that at the moment.\n"
    And Flo should see "***\nYou hear the roll of thunder in the distance!  Joe is making false\nofferings to the Goddess Tashanna!\n"

    # Offer that isn't next birthstone.
    Then Joe should have emerald in inventory
    When Joe performs "offer emerald"
    Then "Joe" should not have "emerald" in inventory
    Then Joe should see "...The Goddess accepts your offer, but in your soul you realize that your\noffering was not one of your birthstones, or was out of sequence.\n"
    And Flo should see "***\nJoe is making offerings to the Goddess Tashanna!\n"

    # Offer that is next birthstone.
    Then Joe should have garnet in inventory
    When Joe performs "offer garnet"
    # Player had 2, so should still have one.
    Then "Joe" should have "garnet" in inventory
    Then Joe should see "...The Goddess Tashanna accepts the offer of your birthstone!  You feel the\nurge to complete the offering with the rest of your birthstones.\n"
    And Flo should see "***\nJoe is making offerings to the Goddess Tashanna!\n"

    # Offer same gem again to make sure it got taken off the birthstone list.
    Then Joe should have garnet in inventory
    When Joe performs "offer garnet"
    Then "Joe" should not have garnet in inventory
    Then Joe should see "...The Goddess accepts your offer, but in your soul you realize that your\noffering was not one of your birthstones, or was out of sequence.\n"
    And Flo should see "***\nJoe is making offerings to the Goddess Tashanna!\n"

    # Offer that is 2nd birthstone.
    Then Joe should have pearl in inventory
    When Joe performs "offer pearl"
    Then "Joe" should not have "pearl" in inventory
    Then Joe should see "...The Goddess Tashanna accepts the offer of your birthstone!  You feel the\nurge to complete the offering with the rest of your birthstones.\n"
    And Flo should see "***\nJoe is making offerings to the Goddess Tashanna!\n"

    # Offer that is 3rd birthstone.
    Then Joe should have bloodstone in inventory
    When Joe performs "offer bloodstone"
    Then "Joe" should not have "bloodstone" in inventory
    Then Joe should see "...The Goddess Tashanna accepts the offer of your birthstone!  You feel the\nurge to complete the offering with the rest of your birthstones.\n"
    And Flo should see "***\nJoe is making offerings to the Goddess Tashanna!\n"

    # Offer that is 4th and final birthstone.
    Then Joe should have diamond in inventory
    When Joe performs "offer diamond"
    Then "Joe" should not have "diamond" in inventory
    Then Joe should see "...As you offer your fourth birthstone to the Goddess Tashanna, you feel\na powerful surge of magical energy course through your body!\n***\nYou are now at level 4!\n***\nA spell has been added to your spellbook!\n"
    And Flo should see "***\nJoe seems to grow in strength and power!\n"
    And Joe should be level 4
    And "Joe" should have the spell "hotseat"
