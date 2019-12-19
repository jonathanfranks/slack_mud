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
      | kyrandia_profile_Flo | Flo          | 1                        | 2                    | 11                  |

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
      | Moe   | kyrandia   | Location 186   | wand,kyragem    | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   |

    When Joe performs "chant nothing"
    Then Joe should see "Nothing happens."

    # With no chant, nothing happens.
    And the opensesame is set to 0
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
    And Flo should see "***\nJoe has just vanished in a golden flash of light!\n"
    And Joe should be in "Location 186"
    And Moe should see "***\nJoe has just appeared in a golden flash of light!\n"

  @cry @ash @shard
  Scenario: Crying at the ash tree
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 26    | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 26    | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   |
    And "Location 26" has no "shard"
    And "shard" item should not be in "Location 26"

    When Joe performs "cry tree"
    Then Joe should see "...As you cry your tears for the sorrow of the ash trees, they fall to the\nground and magically transform into a beautiful crystal shard!\n"
    And Flo should see "***\nA shard suddenly appears on the ground near the ash trees!\n"
    And "shard" item should be in "Location 26"

  @dig @brook
  Scenario: Digging in the brook
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 12    | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 12    | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 2                    | 101                 |

    And the Kyrandia random number will generate 5
    And Joe should have 1 gold
    When Joe performs "dig in brook"
    Then Joe should see "...You find 5 pieces of gold in the brook!\n"
    And Flo should see "***\nJoe is searching the brook for something.\n"
    And Joe should have 6 gold

    And Flo should have 101 gold
    When Flo performs "dig in brook"
    Then Flo should see "...You search the brook, but don't find anything this time.\n"
    And Joe should see "***\nFlo is searching the brook for something.\n"
    And Flo should have 101 gold

    And the Kyrandia random number will generate 50
    And Joe should have 6 gold
    When Joe performs "dig in brook"
    Then Joe should see "...You search the brook, but don't find anything this time.\n"
    And Flo should see "***\nJoe is searching the brook for something.\n"
    And Joe should have 6 gold

    And Flo should have 101 gold
    When Flo performs "dig in brook"
    Then Flo should see "...You search the brook, but don't find anything this time.\n"
    And Joe should see "***\nFlo is searching the brook for something.\n"
    And Flo should have 101 gold

  @dig @beach
  Scenario: Digging in the beach
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 189   | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 189   | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 2                    | 101                 |

    And the Kyrandia random number will generate 5
    And Joe should have 1 gold
    When Joe performs "dig in sand"
    Then Joe should see "...You dig through the sand and happen to find a piece of gold!\n"
    And Flo should see "***\nJoe is digging through the sand.\n"
    And Joe should have 2 gold

    And Flo should have 101 gold
    When Flo performs "dig in sand"
    Then Flo should see "...You dig through the sand and happen to find a piece of gold!\n"
    And Joe should see "***\nFlo is digging through the sand.\n"
    And Flo should have 102 gold

    And the Kyrandia random number will generate 50
    And Joe should have 2 gold
    When Joe performs "dig in sand"
    Then Joe should see "...You dig through the sand, but you find nothing of interest!\n"
    And Flo should see "***\nJoe is digging through the sand.\n"
    And Joe should have 2 gold

    And Flo should have 102 gold
    When Flo performs "dig in sand"
    Then Flo should see "...You dig through the sand, but you find nothing of interest!\n"
    And Joe should see "***\nFlo is digging through the sand.\n"
    And Flo should have 102 gold

  @drink
  Scenario: Drink
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 12    | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 12    | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 2                    | 101                 |

    When Joe performs "drink water"
    Then Joe should see "...The fresh water is very delicious and refreshing!\n"
    And Flo should see "***\nJoe is drinking the water with a refreshed smile.\n"

  @enter @portal1
  Scenario: Enter portal 1
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 184   | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 184   | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 2                    | 101                 |

    And the Kyrandia random number will generate 1
    When Joe performs "enter portal"
    Then Joe should see
    """
    ...As you walk into the portal, you are suddenly blinded by spectacular and
    colorful images beyond anything you have ever seen before!  As your eyes
    start to adjust to the brilliance, a magical scene appears before you...
    ***

    """

    And Joe should see
    """
    You're standing near a beautiful waterfall in the center of an underground
    forest.  Memories of young loves appear before you, so realistic you feel as
    if you could reach out and touch them.  Your heart begins to race faster and
    faster...

    """

    And Joe should see
    """
    ***
    Suddenly you are thrown back into the chamber as the vision vanishes before
    your eyes!

    """

    And Flo should see
    """
    ***
    Joe enters the portal in a blinding flash of light!
    ***
    Suddenly, he is thrown from the portal back into this chamber!

    """


  @enter @portal2
  Scenario: Enter portal 2
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 184   | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 184   | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 2                    | 101                 |

    And the Kyrandia random number will generate 2
    When Joe performs "enter portal"
    Then Joe should see
    """
    ...As you walk into the portal, you are suddenly blinded by spectacular and
    colorful images beyond anything you have ever seen before!  As your eyes
    start to adjust to the brilliance, a magical scene appears before you...
    ***

    """

    And Joe should see
    """
    You're lying in a small, wooden chapel.  Next to you, you feel the warm touch
    of your life's eternal love, holding you close.  All you can remember is the
    dark wood of the rafters above as you journey to paradise...

    """

    And Joe should see
    """
    ***
    Suddenly you are thrown back into the chamber as the vision vanishes before
    your eyes!

    """

    And Flo should see
    """
    ***
    Joe enters the portal in a blinding flash of light!
    ***
    Suddenly, he is thrown from the portal back into this chamber!

    """

  @enter @portal3
  Scenario: Enter portal 3
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 184   | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 184   | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 2                    | 101                 |

    And the Kyrandia random number will generate 3
    When Joe performs "enter portal"
    Then Joe should see
    """
    ...As you walk into the portal, you are suddenly blinded by spectacular and
    colorful images beyond anything you have ever seen before!  As your eyes
    start to adjust to the brilliance, a magical scene appears before you...
    ***

    """

    And Joe should see
    """
    You're crying on the floor, begging for the return of the love you lost long
    ago.  Tears fall like raindrops from your red, swollen eyes as your heart
    breaks from the sorrow of losing the meaning and inspiration of your life...

    """

    And Joe should see
    """
    ***
    Suddenly you are thrown back into the chamber as the vision vanishes before
    your eyes!

    """

    And Flo should see
    """
    ***
    Joe enters the portal in a blinding flash of light!
    ***
    Suddenly, he is thrown from the portal back into this chamber!

    """

  @enter @portal4
  Scenario: Enter portal 4
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 184   | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 184   | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 2                    | 101                 |

    And the Kyrandia random number will generate 4
    When Joe performs "enter portal"
    Then Joe should see
    """
    ...As you walk into the portal, you are suddenly blinded by spectacular and
    colorful images beyond anything you have ever seen before!  As your eyes
    start to adjust to the brilliance, a magical scene appears before you...
    ***

    """

    And Joe should see
    """
    You're standing alone in solitude on a dark, windy beach.  You look across the
    black horizon remembering times of happiness and peace long ago, as the world
    moves on emotionlessly...

    """

    And Joe should see
    """
    ***
    Suddenly you are thrown back into the chamber as the vision vanishes before
    your eyes!

    """

    And Flo should see
    """
    ***
    Joe enters the portal in a blinding flash of light!
    ***
    Suddenly, he is thrown from the portal back into this chamber!

    """

  @enter @portal5
  Scenario: Enter portal 5
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 184   | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 184   | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 2                    | 101                 |

    And the Kyrandia random number will generate 5
    When Joe performs "enter portal"
    Then Joe should see
    """
    ...As you walk into the portal, you are suddenly blinded by spectacular and
    colorful images beyond anything you have ever seen before!  As your eyes
    start to adjust to the brilliance, a magical scene appears before you...
    ***

    """

    And Joe should see
    """
    You're standing by a statue which you dimly recognize in your subconscious, but
    your mind is filled with thoughts of determination, inspiration, and above all
    else, hope.  You feel a power growing within your soul, as a smile returns to
    your face, and a sparkle lights in your eyes...

    """

    And Joe should see
    """
    ***
    Suddenly you are thrown back into the chamber as the vision vanishes before
    your eyes!

    """

    And Flo should see
    """
    ***
    Joe enters the portal in a blinding flash of light!
    ***
    Suddenly, he is thrown from the portal back into this chamber!

    """

  @enter @portal6
  Scenario: Enter portal 6
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 184   | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 184   | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 2                    | 101                 |

    And the Kyrandia random number will generate 6
    When Joe performs "enter portal"
    Then Joe should see
    """
    ...As you walk into the portal, you are suddenly blinded by spectacular and
    colorful images beyond anything you have ever seen before!  As your eyes
    start to adjust to the brilliance, a magical scene appears before you...
    ***

    """

    And Joe should see
    """
    You're flying through the air on the wings of a pegasus, soaring high above
    the forest below.  It is the most exhilarating feeling of freedom you could
    have ever imagined...

    """

    And Joe should see
    """
    ***
    Suddenly you are thrown back into the chamber as the vision vanishes before
    your eyes!

    """

    And Flo should see
    """
    ***
    Joe enters the portal in a blinding flash of light!
    ***
    Suddenly, he is thrown from the portal back into this chamber!

    """

  @enter @portal7
  Scenario: Enter portal 7
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 184   | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 184   | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 2                    | 101                 |

    And the Kyrandia random number will generate 7
    When Joe performs "enter portal"
    Then Joe should see
    """
    ...As you walk into the portal, you are suddenly blinded by spectacular and
    colorful images beyond anything you have ever seen before!  As your eyes
    start to adjust to the brilliance, a magical scene appears before you...
    ***

    """

    And Joe should see
    """
    You're riding a magical carnival ride, holding the hand of your dearest love.
    As you soar through the evening sky, you experience the joy of heaven...

    """

    And Joe should see
    """
    ***
    Suddenly you are thrown back into the chamber as the vision vanishes before
    your eyes!

    """

    And Flo should see
    """
    ***
    Joe enters the portal in a blinding flash of light!
    ***
    Suddenly, he is thrown from the portal back into this chamber!

    """

  @enter @portal8
  Scenario: Enter portal 8
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 184   | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 184   | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 2                    | 101                 |

    And the Kyrandia random number will generate 8
    When Joe performs "enter portal"
    Then Joe should see
    """
    ...As you walk into the portal, you are suddenly blinded by spectacular and
    colorful images beyond anything you have ever seen before!  As your eyes
    start to adjust to the brilliance, a magical scene appears before you...
    ***

    """

    And Joe should see
    """
    A single name echos through your mind: Kyra.

    """

    And Joe should see
    """
    ***
    Suddenly you are thrown back into the chamber as the vision vanishes before
    your eyes!

    """

    And Flo should see
    """
    ***
    Joe enters the portal in a blinding flash of light!
    ***
    Suddenly, he is thrown from the portal back into this chamber!

    """

  @enter @portal9
  Scenario: Enter portal 9
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 184   | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 184   | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 2                    | 101                 |

    And the Kyrandia random number will generate 9
    When Joe performs "enter portal"
    Then Joe should see
    """
    ...As you walk into the portal, you are suddenly blinded by spectacular and
    colorful images beyond anything you have ever seen before!  As your eyes
    start to adjust to the brilliance, a magical scene appears before you...
    ***

    """

    And Joe should see
    """
    Voices vibrate through your head, whispering the words: "Across time and
    space, I love you forever..."

    """

    And Joe should see
    """
    ***
    Suddenly you are thrown back into the chamber as the vision vanishes before
    your eyes!

    """

    And Flo should see
    """
    ***
    Joe enters the portal in a blinding flash of light!
    ***
    Suddenly, he is thrown from the portal back into this chamber!

    """

  @hits
  Scenario: Hits command
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 184   | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 184   | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 2                    | 101                 |

    When Joe performs "hits"
    Then Joe should see "...You have 100 out of 100 hit points remaining.\n"
    And Flo should see "***\nJoe is is checking his health.\n"

  @imagine @dagger
  Scenario: Imagine dagger
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 181   | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 181   | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 2                    | 101                 |

    When Joe performs "imagine dagger"
    Then Joe should see "...As you concentrate upon your wish, the Goddess Tashanna intervenes with\nher magic, and a dagger appears in your hands!\n"
    And Flo should see "***\nJoe is concentrating with sincere determination.\n"

  @marry @temple
  Scenario: Marry other player
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 7     | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 7     | wand            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 7     | wand            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 1                   |

    When Joe performs "marry flo"
    Then Joe should see "...You devote the rest of your mortal life in Kyrandia to Flo.\n"
    And Flo should see "***\nJoe has decided to devote the rest of his mortal life in Kyrandia to you!\n"
    And Moe should see "***\nJoe has decided to devote the rest of his mortal life in Kyrandia to\nFlo!\n"

    # Now try to marry again.
    When Joe performs "marry moe"
    Then Joe should see "...What bigamy is this?  You've already sworn your life to Flo!  Surely\nyou have more dedication than that!\n"
    And Flo should see "***\nJoe is looking like a nasty polygamist!\n"
    And Moe should see "***\nJoe is looking like a nasty polygamist!\n"

  @marry @temple
  Scenario: Marry player that isn't there
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 7     | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 0     | wand            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 7     | wand            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 1                   |

    When Joe performs "marry flo"
    Then Joe should see "...Feeling somewhat lonely, huh?  Sorry, that person isn't around...\n"
    And Moe should see "***\nJoe is feeling somewhat romantically amorous.\n"

  @marry @temple @self
  Scenario: Marry self
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 7     | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 0     | wand            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 7     | wand            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 1                   |

    When Joe performs "marry joe"
    Then Joe should see "...Trying to marry yourself, huh?  Sorry, we don't allow that sort of self\npleasure in Kyrandia...\n"
    And Moe should see "***\nJoe is looking somewhat skeptical about a new way of life.\n"

  @offer @gold @temple
  Scenario: Offer gold at temple
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 7     | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 7     | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 1                   |

    When Joe performs "offer 10 gold"
    Then Joe should see "...Unfortunately, you do not have that in gold.  Best beware of making false\noffers to the gods!\n"
    And Flo should see "***\nJoe is looking somewhat cheap.\n"

    When Joe performs "offer 1 gold"
    Then Joe should see "...As you make you offering, a flash of lightning streaks from the sky and\nstrikes your offered gold, disintegrating it!\n***\nThe Goddess Tashanna appears to you and graciously thanks you for your\nwonderful sacrifice.\n"
    And Flo should see "***\nA flash of lightning streaks from the sky and strikes an offering of gold\nfrom Joe!\n"

  @pick @rose
  Scenario: Picking roses
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 12    | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 12    | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 1                   |

    When Joe performs "pick rose"
    Then Joe should see "...You pick one of the beautiful, lavender roses and add it to your possessions!\n"
    And Flo should see "***\nJoe has just picked one of the lavender roses.\n"
    And Joe should have rose in inventory

  @pick @pinecone
  Scenario: Picking pinecones
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 14    | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 14    | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 1                   |
    And the Kyrandia random number will generate 1
    When Joe performs "pick pinecone"
    Then Joe should see "...You successfully grab one of the pinecones!\n"
    And Flo should see "***\nJoe has successfully grabbed one of the pinecones from the tree!\n"
    And Joe should have pinecone in inventory

    And the Kyrandia random number will generate 100
    When Joe performs "pick pinecone"
    Then Joe should see "...You miss!\n"
    And Flo should see "***\nJoe is trying to grab a pinecone without much success.\n"
    And Joe should have pinecone in inventory

  @pick @ruby
  Scenario: Picking rubies
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 20    | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 20    | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 4                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 1                   |
    And the Kyrandia random number will generate 1
    When Joe performs "pick ruby"
    Then Joe should see "...You successfully grab one of the rubies!\n"
    And Flo should see "***\nJoe has successfully grabbed a ruby from the tree!\n"
    And Joe should have ruby in inventory
    And Joe should have 16 hit points

    And the Kyrandia random number will generate 100
    When Joe performs "pick ruby"
    Then Joe should see "...You fail to grab a ruby.\n***\nA snake lunges out from the tree and bites for you for 8 points of damage!\n"
    And Flo should see "***\nJoe tries to grab a ruby, but instead is bitten by a large snake!\n"
    And Joe should have 8 hit points

    And the Kyrandia random number will generate 100
    When Joe performs "pick ruby"
    Then Joe should see "...You fail to grab a ruby.\n***\nA snake lunges out from the tree and bites for you for 8 points of damage!\n"
    And Flo should see "***\nJoe tries to grab a ruby, but instead is bitten by a large snake!\n"
    And Joe should have 4 hit points
    And Joe should be in "Location 0"
    And Joe should be level 1

  @pick @tulip
  Scenario: Picking tulips
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 199   | wand            | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 199   | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 4                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 1                   |

    When Joe performs "pick tulip"
    Then Joe should see "...You pick a beautiful silver tulip!\n"
    And Flo should see "***\nJoe is messing around in the tulips!\n"
    And Joe should have tulip in inventory

  @place @sword @rock
  Scenario: Placing sword in rock
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
    And the current rock pray count is set to 1
    When Joe performs "place sword in rock"
    Then Joe should see "...The rock glows a bright purple in acceptance of your offering, and the\nsword vanishes!\n***\nA tiara suddenly appears in your hands!\n"
    And Flo should see "***\nJoe is messing around with the rock.\n"
    And Joe should not have sword in inventory
    And Joe should have tiara in inventory

    When Joe performs "place sword in rock"
    Then Joe should see "...Hmmmmm, that's an interesting concept, but unfortunately, not an acceptable\none.\n"
    And Flo should see "***\nJoe is messing around with the rock.\n"
    And Joe should not have sword in inventory
    And Joe should have tiara in inventory

  @say @key @lpatgbbtlwnd
  Scenario: Saying the saying to get the key
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 183   | sword           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 183   | wand            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Joe | Joe          | 0                        | 4                    | 1                   |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 1                   |

    When Joe performs "say legends pass and time goes by but true love will never die"
    Then Joe should see "...As you speak the words of eternal strength and acceptance of the supreme\npowers of good in the universe, a golden key suddenly materializes in your\nhands.\n"
    And Flo should see "***\nJoe is suddenly engulfed within a shimmering globe of golden light, which\nradiates brilliantly for a few moments and then vanishes!\n"
    And Joe should have key in inventory

  @sell
  Scenario: Sell gems at gemcutter
    Given player content:
      | title | field_game | field_location | field_inventory            | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 8     | dagger,ruby,garnet,kyragem | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 8     | wand                       | 1            | Flo                | Flo                   |

    When Joe performs "sell nothing"
    Then Joe should see "...Unfortunately, you don't have that at the moment.\n"
    And Flo should see "***\nJoe and the gem cutter move off by themselves for a few minutes.\n"

    When Joe performs "sell dagger"
    Then Joe should see '...The gem cutter says to you, "Thanks, but no thanks."\n'
    And Flo should see "***\nJoe and the gem cutter move off by themselves for a few minutes.\n"
    And Joe should have dagger in inventory

    When Joe performs "sell ruby"
    Then Joe should see "...The gem cutter considers for a moment, takes your gem, and then gives\nyou 22 pieces of gold.\n"
    And Flo should see "***\nJoe trades the gem cutter ruby for 22 pieces of gold.\n"
    And Joe should not have ruby in inventory
    And Joe should have 22 gold

    When Joe performs "sell kyragem"
    Then Joe should see
    """
    ...The gem cutter looks sharply at you, and then suddenly smiles.  He leans
    closer and says, "I bid you good luck, brave seeker of legends".  He takes the
    kyragem and hands you a soulstone.

    """
    And Flo should see "***\nJoe and the gem cutter move off by themselves for a few minutes.\n"
    And Joe should not have kyragem in inventory
    And Joe should have soulstone in inventory
