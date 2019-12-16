@api @registration @login
Feature: Kyrandia commands not specific to locations

  @level @kneel @level2
  Scenario: Level 2
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 0     | garnet,garnet   | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 0     | ruby,diamond    | 1            | Flo                | Flo                   |
    And "Joe" should not have the spell "smokey"
    And "Joe" should be level 1
    When "Joe" performs "kneel"
    Then "Joe" should see '...As you kneel, a vision of the Goddess Tashanna materializes before you.\nShe lays her hand gently upon your shoulder and says to you, "Rise, rise,\nMagic-user!  Your first advancement has begun."  She then vanishes, and\nyou feel yourself grow in power!\n***\nYou are now at level 2!\n***\nA spell has been added to your spellbook!\n'
    And "Flo" should see "***\nJoe seems to suddenly grow in strength and knowledge!\n"
    And "Joe" should have the spell "smokey"
    And "Joe" should be level 2

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

    And "Joe" should be level 2
    When "Joe" performs "glory be to tashanna"
    Then "Joe" should see '...As you praise the Goddess Tashanna, you feel yourself grow in power!\n***\nYou are now at level 3!\n'
    And "Flo" should see "***\nJoe seems to suddenly grow in strength and knowledge!\n"
    And "Joe" should be level 3

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
    And Joe should be level 3

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

  @level @kneel @level5 @deadwoodedglade
  Scenario: Level 5
    Given player content:
      | title | field_game | field_location | field_inventory                                | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 16    | garnet,garnet,pearl,bloodstone,diamond,emerald | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 16    | ruby,diamond                                   | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_birth_stones     |
      | kyrandia_profile_Joe | Joe          | 0                        | 4                    | garnet,pearl,bloodstone,diamond |
    And Joe should be level 4
    When Joe performs "fear no evil"
    Then Joe should see "...As you boldly defy the evil, the Goddess Tashanna rewards you for your\ncourage with more knowledge and power.  You are now level 5!\n"
    And Flo should see "***\nJoe has just gained in wisdom and courage.\n"
    And Joe should be level 5

  @level @kneel @level6 @stump
  Scenario: Level 6
    Given player content:
      | title | field_game | field_location | field_inventory                                                                                             | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 18    | ruby,emerald,emerald,tulip,garnet,pearl,aquamarine,moonstone,sapphire,diamond,amethyst,onyx,opal,bloodstone | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 18    | ruby,diamond                                                                                                | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_birth_stones     |
      | kyrandia_profile_Joe | Joe          | 0                        | 5                    | garnet,pearl,bloodstone,diamond |
    And Joe should be level 5
    # Drop an item player doesn't have.
    When Joe performs "drop key in stump"
    Then Joe should see "...But you don't have one, you hallucinating fool!\n"
    And Flo should see "***\nJoe, who has apparently been smoking too many magic weeds, is having\nhallucinations with the stump!\n"
    # Drop an item that isn't the next stone.
    When Joe performs "drop tulip in stump"
    Then Joe should see "...It drops into the endless depths of the stump, but nothing seems to\nhappen.\n"
    And Flo should see "***\nJoe is dropping things in the stump.\n"
    And Joe should not have tulip in inventory
    # Drop the next stone.
    When Joe performs "drop ruby in stump"
    Then Joe should see "...The gem drops smoothly into the endless depths of the stump.  You\nfeel a mysterious tingle down your spine, as though you have begun to\nunleash a powerful source of magic.\n"
    And Flo should see "***\nJoe is dropping things in the stump.\n"
    And Joe should not have ruby in inventory
    # Drop the next stone.
    When Joe performs "drop emerald in stump"
    Then Joe should see "...The gem drops smoothly into the endless depths of the stump.  You\nfeel a mysterious tingle down your spine, as though you have begun to\nunleash a powerful source of magic.\n"
    And Flo should see "***\nJoe is dropping things in the stump.\n"
    And Joe should have emerald in inventory
    # Repeat this stone to check that it is rejected.
    When Joe performs "drop emerald in stump"
    Then Joe should see "...It drops into the endless depths of the stump, but nothing seems to\nhappen.\n"
    And Flo should see "***\nJoe is dropping things in the stump.\n"
    And Joe should not have emerald in inventory
    # Drop the next stone.
    When Joe performs "drop garnet in stump"
    Then Joe should see "...The gem drops smoothly into the endless depths of the stump.  You\nfeel a mysterious tingle down your spine, as though you have begun to\nunleash a powerful source of magic.\n"
    And Flo should see "***\nJoe is dropping things in the stump.\n"
    And Joe should not have garnet in inventory
    # Drop the next stone.
    When Joe performs "drop pearl in stump"
    Then Joe should see "...The gem drops smoothly into the endless depths of the stump.  You\nfeel a mysterious tingle down your spine, as though you have begun to\nunleash a powerful source of magic.\n"
    And Flo should see "***\nJoe is dropping things in the stump.\n"
    And Joe should not have pearl in inventory
    # Drop the next stone.
    When Joe performs "drop aquamarine in stump"
    Then Joe should see "...The gem drops smoothly into the endless depths of the stump.  You\nfeel a mysterious tingle down your spine, as though you have begun to\nunleash a powerful source of magic.\n"
    And Flo should see "***\nJoe is dropping things in the stump.\n"
    And Joe should not have aquamarine in inventory
    # Drop the next stone.
    When Joe performs "drop moonstone in stump"
    Then Joe should see "...The gem drops smoothly into the endless depths of the stump.  You\nfeel a mysterious tingle down your spine, as though you have begun to\nunleash a powerful source of magic.\n"
    And Flo should see "***\nJoe is dropping things in the stump.\n"
    And Joe should not have moonstone in inventory
    # Drop the next stone.
    When Joe performs "drop sapphire in stump"
    Then Joe should see "...The gem drops smoothly into the endless depths of the stump.  You\nfeel a mysterious tingle down your spine, as though you have begun to\nunleash a powerful source of magic.\n"
    And Flo should see "***\nJoe is dropping things in the stump.\n"
    And Joe should not have sapphire in inventory
    # Drop the next stone.
    When Joe performs "drop diamond in stump"
    Then Joe should see "...The gem drops smoothly into the endless depths of the stump.  You\nfeel a mysterious tingle down your spine, as though you have begun to\nunleash a powerful source of magic.\n"
    And Flo should see "***\nJoe is dropping things in the stump.\n"
    And Joe should not have diamond in inventory
    # Drop the next stone.
    When Joe performs "drop amethyst in stump"
    Then Joe should see "...The gem drops smoothly into the endless depths of the stump.  You\nfeel a mysterious tingle down your spine, as though you have begun to\nunleash a powerful source of magic.\n"
    And Flo should see "***\nJoe is dropping things in the stump.\n"
    And Joe should not have amethyst in inventory
    # Drop the next stone.
    When Joe performs "drop onyx in stump"
    Then Joe should see "...The gem drops smoothly into the endless depths of the stump.  You\nfeel a mysterious tingle down your spine, as though you have begun to\nunleash a powerful source of magic.\n"
    And Flo should see "***\nJoe is dropping things in the stump.\n"
    And Joe should not have onyx in inventory
    # Drop the next stone.
    When Joe performs "drop opal in stump"
    Then Joe should see "...The gem drops smoothly into the endless depths of the stump.  You\nfeel a mysterious tingle down your spine, as though you have begun to\nunleash a powerful source of magic.\n"
    And Flo should see "***\nJoe is dropping things in the stump.\n"
    And Joe should not have opal in inventory
    # Drop the next stone.
    When Joe performs "drop bloodstone in stump"
    Then Joe should see "...As you drop the gem into the stump, a powerful surge of magical energy\nrushes through your entire body!\n***\nYou are now at level 6!\n***\nA spell has been added to your spellbook!\n"
    And Flo should see "***\nJoe has just gained in power and knowledge!\n"
    And Joe should not have bloodstone in inventory
    And Joe should be level 6
    And Joe should have the spell hotkiss

  @level @kneel @level7 @hiddenshrine
  Scenario: Level 7
    Given player content:
      | title | field_game | field_location | field_inventory                                | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 101   | garnet,garnet,pearl,bloodstone,diamond,emerald | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 101   | ruby,diamond                                   | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_birth_stones     |
      | kyrandia_profile_Joe | Joe          | 0                        | 6                    | garnet,pearl,bloodstone,diamond |
    And Joe should be level 6
    When Joe performs "offer heart and soul to tashanna"
    Then Joe should see "...As you offer your heart and soul, the most precious jewels of your life,\nyou feel the hand of the Goddess Tashanna bless you with power.\n***\nYou are now at level 7!\n***\nA spell has been added to your spellbook!\n"
    And Flo should see "***\nJoe has suddenly grown in strength and knowledge!\n"
    And Joe should be level 7
    And Joe should have the spell weewillo

  @level @kneel @level8 @mistyruins
  Scenario: Level 8
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 188   | dagger          | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 188   | ruby,diamond    | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_birth_stones     |
      | kyrandia_profile_Joe | Joe          | 0                        | 7                    | garnet,pearl,bloodstone,diamond |
    And Joe should be level 7
    When Joe performs "place dagger in orb"
    Then Joe should see "...The orb accepts your offer, and glows brightly for a moment!\n***\nYou are now at level 8!\n"
    And Flo should see "***\nJoe is attempting to touch the orb!\n"
    And Joe should be level 8
    And Joe should not have dagger in inventory

  @level @kneel @level9 @mistyruins
  Scenario: Level 9
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 7   | dagger          | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 7   | ruby,diamond    | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_birth_stones     |
      | kyrandia_profile_Joe | Joe          | 0                        | 8                    | garnet,pearl,bloodstone,diamond |
    And Joe should be level 8
    And the current temple chant count is 0

    When Joe performs "place dagger in orb"
    Then Joe should see "...The orb accepts your offer, and glows brightly for a moment!\n***\nYou are now at level 8!\n"
    And Flo should see "***\nJoe is attempting to touch the orb!\n"
    And Joe should be level 9
    And Joe should not have dagger in inventory
