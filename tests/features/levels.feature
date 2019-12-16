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
      | Joe   | kyrandia   | Location 7     | dagger,charm    | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 7     | ruby,diamond    | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_birth_stones     |
      | kyrandia_profile_Joe | Joe          | 0                        | 8                    | garnet,pearl,bloodstone,diamond |
    And Joe should be level 8
    And the current temple chant count is set to 0
    When Joe performs "chant tashanna"
    Then Joe should see "The altar begins to glow dimly."
    And Flo should see "The altar begins to glow dimly."
    And the current temple chant count should be 1
    When Joe performs "chant tashanna"
    Then Joe should see "The altar begins to glow even brighter!"
    And Flo should see "The altar begins to glow even brighter!"
    And the current temple chant count should be 2
    When Joe performs "chant tashanna"
    Then Joe should see "The altar begins to glow even brighter!"
    And Flo should see "The altar begins to glow even brighter!"
    And the current temple chant count should be 3
    When Joe performs "chant tashanna"
    Then Joe should see "The altar begins to glow even brighter!"
    And Flo should see "The altar begins to glow even brighter!"
    And the current temple chant count should be 4
    When Joe performs "chant tashanna"
    Then Joe should see "The altar begins to glow even brighter!"
    And Flo should see "The altar begins to glow even brighter!"
    And the current temple chant count should be 5
    And Joe performs "place charm on altar"
    Then Joe should see "...Tashanna accepts your offer!\n***\nYou are now at level 9!\n"
    And Flo should see "***\nJoe suddenly grows in strength and knowledge!\n"
    And Joe should be level 9
    And Joe should not have charm in inventory

  @level @kneel @level10 @mistyruins
  Scenario: Level 10
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 7     | dagger,tiara    | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 7     | ruby,diamond    | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_birth_stones     |
      | kyrandia_profile_Joe | Joe          | 0                        | 9                    | garnet,pearl,bloodstone,diamond |
    And Joe should be level 9
    And the current temple chant count is set to 0
    When Joe performs "chant tashanna"
    Then Joe should see "The altar begins to glow dimly."
    And Flo should see "The altar begins to glow dimly."
    And the current temple chant count should be 1
    When Joe performs "chant tashanna"
    Then Joe should see "The altar begins to glow even brighter!"
    And Flo should see "The altar begins to glow even brighter!"
    And the current temple chant count should be 2
    When Joe performs "chant tashanna"
    Then Joe should see "The altar begins to glow even brighter!"
    And Flo should see "The altar begins to glow even brighter!"
    And the current temple chant count should be 3
    When Joe performs "chant tashanna"
    Then Joe should see "The altar begins to glow even brighter!"
    And Flo should see "The altar begins to glow even brighter!"
    And the current temple chant count should be 4
    When Joe performs "chant tashanna"
    Then Joe should see "The altar begins to glow even brighter!"
    And Flo should see "The altar begins to glow even brighter!"
    And the current temple chant count should be 5
    And Joe performs "place tiara on altar"
    Then Joe should see "...Tashanna graciously accepts your gift.\n***\nYou are now at level 10!\n"
    And Flo should see "***\nJoe suddenly grows in strength and knowledge!\n"
    And Joe should be level 10
    And Joe should not have tiara in inventory

  @level @kneel @level11 @crystaltree
  Scenario: Level 11
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 201   | dagger,wand     | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 201   | ruby,diamond    | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_birth_stones     |
      | kyrandia_profile_Joe | Joe          | 0                        | 10                   | garnet,pearl,bloodstone,diamond |
    And Joe should be level 10
    And Joe performs "aim wand at tree"
    And Joe should see "...As you aim the wand at the crystal tree, there's a flash of silver light!\n***\nYou are now at level 11!\n"
    And Flo should see '***\nJoe is staring at the crystal tree with "oohs" and "ahhs".\n'
    And Joe should be level 11

  @level @kneel @level12 @crystaltree
  Scenario: Level 12
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | dagger,kyragem  | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | ruby,diamond    | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_birth_stones     |
      | kyrandia_profile_Joe | Joe          | 0                        | 11                   | garnet,pearl,bloodstone,diamond |
    And Joe should be level 11
    And Joe performs "offer kyragem"
    And Joe should see "...As you offer the kyragem, you feel a warmth surround you.\n***\nYou are now at level 12!\n"
    And Flo should see "***\nJoe is smiling with joy.\n"
    And Joe should be level 12

  @level @kneel @level13 @chamberofthebody
  Scenario: Level 13 - Protected
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 282   | dagger,kyragem  | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 282   | ruby,diamond    | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_birth_stones     | field_kyrandia_spellbook | field_kyrandia_memorized_spells |
      | kyrandia_profile_Joe | Joe          | 0                        | 12                   | garnet,pearl,bloodstone,diamond | abbracada                | abbracada                       |
    And Joe should be level 12
    And Joe performs "cast abbracada"
    And Joe should see "...As you cast the spell, a flash of bright blue light encircles you for a\nbrief moment!  You suddenly feel mystically protected.\n"
    And Flo should see "***\nJoe casts a spells, and is suddenly encircled in a flash of bright blue\nlight for a brief moment!\n"
    And Joe performs "jump across chasm"
    And Joe should see "...You leap with all your might and cross the chasm!\n***\nA broach appears among your possesions!\n***\nYou are now at level 13!\n"
    And Flo should see "***\nJoe has just successfully leaped across the chasm!\n"
    And Joe should be level 13
    And Joe should have broach in inventory

  @level @kneel @level13 @chamberofthebody
  Scenario: Level 13 - Unprotected
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 282   | dagger,kyragem  | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 282   | ruby,diamond    | 1            | Flo                | Flo                   |
      | Mo    | kyrandia   | Location 0     | ruby,diamond    | 1            | Mo                 | Mo                    |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_birth_stones     |
      | kyrandia_profile_Joe | Joe          | 0                        | 12                   | garnet,pearl,bloodstone,diamond |
    And Joe should be level 12
    And Joe performs "jump across chasm"
    And Joe should see "...You leap with all your might, and you almost make the other side, but at\nthe last inch, you slip and fall backwards, into the endless depths of the\nchasm...\n"
    And Flo should see "***\nJoe attempts to jump the chasm, but unfortunately falls to certain death...\n"
    And Joe should see "***\nSuddenly, everything goes black, and you feel yourself falling through a deep\nchasm.  Strange colors flash in your mind and your ears are deafened with the\nsound of rolling thunder.  After what seems like an eternity, you finally feel\nyourself floating gently to the ground, and your vision returns...\n\n"
    And Flo should see "***\nJoe just ceased to exist, having been sucked into a vanishing void.\n"
    And Joe should be level 1
    And Joe should be in "Location 0"
    And Mo should see "***\nJoe has just appeared in a holy light!\n"
    And Joe should not have broach in inventory

  @level @kneel @level14 @chamberofthebody
  Scenario: Level 14
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 285   | soulstone       | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 285   | diamond         | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level |
      | kyrandia_profile_Joe | Joe          | 0                        | 13                   |
    And Joe should be level 13
    And Joe performs "answer time"
    And Joe should see '...An emotionless voice responds: "Correct."\n***\nA pendant appears in your hands!\n***\nYou are now at level 14!\n'
    And Flo should see "***\nJoe has suddenly grown in strength and knowledge.\n"
    And Joe should be level 14
    And Joe should have pendant in inventory

  @level @kneel @level15 @chamberoftheheart
  Scenario: Level 15
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 288   | soulstone       | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 288   | diamond         | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level |
      | kyrandia_profile_Joe | Joe          | 0                        | 14                   |
    And Joe should be level 14
    And Joe is married to Flo
    And Joe should be married to Flo
    And Joe performs "offer heart to flo"
    And Joe should see "...Your commitment to your heart's love proves you worthy of greater\nknowledge.\n***\nA locket suddenly appears in your hands!\n***\nYou are now at level 15!\n"
    And Flo should see "***\nJoe is momentarily surrounded by a dark red glow.\n"
    And Joe should be level 15
    And Joe should have locket in inventory

  @level @kneel @level16 @chamberofthesoul
  Scenario: Level 16
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 291   | soulstone       | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 291   | diamond         | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level |
      | kyrandia_profile_Joe | Joe          | 0                        | 15                   |
    And Joe should be level 15
    And Joe performs "ignore time"
    And Joe should see "...Your realization that time has no effect upon the immortal soul\nproves you worthy of greater enlightnment.\n***\nYou are now at level 16!\n***\nYou also receive a ring, the symbol of universal unity, as a mark of\nyour achievement.\n"
    And Flo should see "***\nJoe has suddenly grown in strength and wisdom.\n"
    And Joe should be level 16
    And Joe should have ring in inventory

  @level @kneel @level17 @chamberoflife
  Scenario: Level 17 - does not have items
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 295   | soulstone,ring  | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 295   | diamond         | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level |
      | kyrandia_profile_Joe | Joe          | 0                        | 16                   |
    And Joe should be level 16
    And Joe performs "devote"
    And Joe should see "...Your devotion is not complete.\n"
    And Flo should see "***\nJoe is looking rather disappointed.\n"
    And Joe should be level 16
    And Joe should have ring in inventory

  @level @kneel @level17 @chamberoflife
  Scenario: Level 17 - does have items
    Given player content:
      | title | field_game | field_location | field_inventory                      | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 295   | soulstone,ring,broach,locket,pendant | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 295   | diamond                              | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level |
      | kyrandia_profile_Joe | Joe          | 0                        | 16                   |
    And Joe should be level 16
    And Joe performs "devote"
    And Joe should see "...You devote yourself...\n***\nYour broach vanishes in a red flash!\n***\nYour pendant vanishes in a blue flash!\n***\nYour locket vanishes in a golden flash!\n***\nYour ring vanishes in a purple flash!\n***\nYou are now at level 17!\n"
    And Flo should see "***\nJoe is suddenly surrounded by a rainbow variety of flashes of light!\n"
    And Joe should be level 17
    And Joe should not have ring in inventory
    And Joe should not have broach in inventory
    And Joe should not have locket in inventory
    And Joe should not have pendant in inventory

  @level @kneel @level18 @chamberoftruth
  Scenario: Level 18 - Pass
    Given player content:
      | title | field_game | field_location | field_inventory                      | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 280   | soulstone,ring,broach,locket,pendant | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 280   | diamond                              | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level |
      | kyrandia_profile_Joe | Joe          | 0                        | 17                   |
    And Joe should be level 17
    And the Kyrandia random number will generate 51
    And Joe performs "seek truth"
    And Joe should see '...As you seek the truth, the Goddess Tashanna, in all her glory and beauty\nappears before you and says: "This is your test to become a Mage of Fire.\nI wish you the best of luck."\n***\nThe Goddess then vanishes.\n***\nSuddenly, you feel yourself in a game of tug-o-war.  You fight with all\nyour effort against your invisible opponent, trying to win.  You feel\nyourself slipping, and losing the battle, when suddenly an inner strength\npulls you to victory!\n***\nYou are now at level 18!\n'
    And Flo should see "***\nJoe is looking a little peaked.\n"
    And Joe should be level 18

  @level @kneel @level18 @chamberoftruth
  Scenario: Level 18 - Fail
    Given player content:
      | title | field_game | field_location | field_inventory                      | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 280   | soulstone,ring,broach,locket,pendant | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 280   | diamond                              | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level |
      | kyrandia_profile_Joe | Joe          | 0                        | 17                   |
    And Joe should be level 17
    And the Kyrandia random number will generate 1
    And Joe performs "seek truth"
    And Joe should see '...As you seek the truth, the Goddess Tashanna, in all her glory and beauty\nappears before you and says: "This is your test to become a Mage of Fire.\nI wish you the best of luck."\n***\nThe Goddess then vanishes.\n***\nSuddenly, you feel yourself in a game of tug-o-war.  You fight with all\nyour effort against your invisible opponent, trying to win.  You feel\nyourself starting to pull your opponent to your victory, when suddenly\nyou slip and fall...\n'
    And Flo should see "***\nJoe just ceased to exist, having been sucked into a vanishing void.\n"
    And Joe should be level 1
    And Joe should be in "Location 0"

