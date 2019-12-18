@api
Feature: Kyrandia commands not specific to locations

  @aim
  Scenario: Aim
    Given player content:
      | title | field_game | field_location | field_inventory                      | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 302   | soulstone,ring,broach,locket,pendant | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 302   | diamond                              | 1            | Flo                | Flo                   |

    When Joe performs "aim"
    Then Joe should see "...Okay, if you say so!\n"
    And Flo should see "Joe is pointing wildly."

    When Joe performs "aim soulstone"
    Then Joe should see "...At who?!??!\n"
    And Flo should see "Joe is waving his arms."

    When Joe performs "aim soulstone at flo"
    Then Joe should see "...How thoughtful of you!\n"
    And Flo should see "Joe is waving obscenely!"

    When Joe performs "aim soulstone at nobody"
    Then Joe should see "...At WHO?!??!  Huh?\n"
    And Flo should see "Joe is seeing ghosts!"

    When Joe performs "aim something at nobody"
    Then Joe should see "...Ooooookay!\n"
    And Flo should see "Joe is playing with his body parts!"

  @break
  Scenario: Break
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 204   | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 204   | wand,kyragem    | 1            | Flo                | Flo                   |

    And Joe should not have kyragem in inventory
    When Joe performs "break wand"
    Then Joe should see '...The wand breaks in two.\n***\nSuddenly, a the goddess Tashanna appears in holy beauty before you.  She smiles\nkindly at you and places a legendary kyragem in your hands.  "Take care, fair\none," she whispers to you before vanishing!\n'
    And Flo should see "***\nJoe is momentarily surrounded by a circle of shimmering rainbows!\n"
    And Joe should have kyragem in inventory
    And Joe should not have wand in inventory

    And Flo should have wand in inventory
    And Flo should have kyragem in inventory
    When Flo performs "break wand"
    Then Flo should see "...The wand breaks in two.\n"
    And Joe should see "***\nFlo is looking rather disappointed.\n"
    And Joe should have kyragem in inventory
    And Joe should not have wand in inventory

  @count
  Scenario: Count
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 204   | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 204   | wand,kyragem    | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Flo          | 1                        | 2                    | 11                  |

    When Joe performs "count"
    Then Joe should see "...But what?\n"

    When Joe performs "count nothing"
    Then Joe should see "...Naah, YOU do it!\n"

    When Joe performs "count gold"
    Then Joe should see "...You have 1 gold piece!\n"
    And Flo should see "Joe is counting his gold."

    When Flo performs "count gold"
    Then Flo should see "...You have 11 gold pieces!\n"
    And Joe should see "Flo is counting her gold."

  @buy
  Scenario: Buy
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 9     | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 9     | wand,kyragem    | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Flo          | 1                        | 2                    | 1100                |

    When Joe performs "buy weewillo"
    Then Joe should see
  """
  ...The shop keeper smirks at you and says, "Sorry, but you're going to need\nmore gold for THAT spell."\n
  """
    And Flo should see "***\nThe shop keeper and Joe seem to be haggling for a few minutes, but the\nshop keeper finally turns his back on him.\n"

    When Joe performs "buy nonsense"
    Then Joe should see
    """
    ...The shop keeper sadly says to you, "Sorry, but we don't have that spell\nin stock... I'll try and order it for you by, oh, say sometime in the next\nsix hundred years!  Have a good day."\n
    """
    And Flo should see "***\nThe shop keeper and Joe seem to be haggling for a few minutes, but the\nshop keeper finally turns his back on him.\n"

    When Flo performs "buy nonsense"
    Then Flo should see
    """
    ...The shop keeper sadly says to you, "Sorry, but we don't have that spell\nin stock... I'll try and order it for you by, oh, say sometime in the next\nsix hundred years!  Have a good day."\n
    """
    And Joe should see "***\nThe shop keeper and Flo seem to be haggling for a few minutes, but the\nshop keeper finally turns his back on her.\n"

    And Flo should not have the spell weewillo
    And Flo should have 1100 gold
    When Flo performs "buy weewillo"
    Then Flo should see
    """
...The shop keeper smiles, takes your gold, and says, "Thank you!".
***
He then waves his hands and a bright purplish globe of light envelops your
spellbook for a moment!

    """
    And Joe should see "***\nThe shop keeper and Flo haggle for a few minutes, then the shop keeper\ntakes her gold and casts an incantation upon Flo's spellbook.\n"
    And Flo should have the spell weewillo
    And Flo should have 980 gold

    # Buy the same spell again, transaction should go through.
    When Flo performs "buy weewillo"
    Then Flo should see
    """
...The shop keeper smiles, takes your gold, and says, "Thank you!".
***
He then waves his hands and a bright purplish globe of light envelops your
spellbook for a moment!

    """
    And Joe should see "***\nThe shop keeper and Flo haggle for a few minutes, then the shop keeper\ntakes her gold and casts an incantation upon Flo's spellbook.\n"
    And Flo should have the spell weewillo
    And Flo should have 860 gold

  @chant @opensesame @alcove @key
  Scenario: Chant opensesame at the alcove and put the key in the crevice
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 185   | wand,key        | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 185   | wand,kyragem    | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   |

    When Joe performs "chant nothing"
    Then Joe should see "Nothing happens."

    # With no chant, nothing happens.
    And the opensesame is set to false
    When Joe performs "place key in crevice"
    Then Joe should see "...For some reason, nothing happens at all!\n"
    And Joe should have key in inventory
    And Flo should see "***\nOddly enough, Joe is playing around with the wall.\n"
    And Joe should be in "Location 185"

    When Joe performs "chant opensesame"
    Then the opensesame should be true
    Then Joe should see "...As you chant the words, the wall suddenly begins to glow with a\nshimmering, golden hue!\n"
    And Flo should see "***\nSuddenly, the wall begins to glow with a shimmering, golden hue!\n"

    When Joe performs "place key in crevice"
    Then Joe should see "...As you drop the key into the crevice, a flash of golden light engulfs you,\nand you feel yourself being magically transported through space...\n"
    And Joe should not have key in inventory
    And Flo should see "***\nJoe has just vanished in a flash of golden light!\n"
    And Joe should be in "Location 186"
