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

  @spellscommand
  Scenario: Spells command
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

    When Joe performs "spells"
    Then Joe should see 'You currently have no spells memorized, and 50 spell points of energy. You are at level 25, titled "Arch-Mage of Legends".'

  @zapher @wand @tulip
  Scenario: Casting zapher on a tulip
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook |
      | kyrandia_profile_Joe | Joe          | 0                        | 15                   | 1                   | zapher                   |
      | kyrandia_profile_Flo | Flo          | 1                        | 15                   | 101                 | zapher                   |

    When Joe performs "learn zapher"
    And Joe performs "cast zapher tulip"
    Then Joe should see "...As you cast the zapher spell, the tulip vanishes and a silver wand appears\nin your hands!\n"
    And Flo should see "***\nJoe is attempting to cast a spell.\n"
    And Joe should not have tulip in inventory
    And Joe should have wand in inventory

  @notmem
  Scenario: Casting spell not memorized
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook |
      | kyrandia_profile_Joe | Joe          | 0                        | 15                   | 1                   | zapher                   |
      | kyrandia_profile_Flo | Flo          | 1                        | 15                   | 101                 | zapher                   |

    When Joe performs "cast zapher"
    Then Joe should see "...You concentrate upon your words of magic, yet nothing happens!  You cannot\nseem to find that spell memorized in your mind.\n"
    And Flo should see "***\nJoe is trying to cast a spell, without success.\n"

  @toolow
  Scenario: Casting spell too high level
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook |
      | kyrandia_profile_Joe | Joe          | 0                        | 1                    | 1                   | zapher                   |
      | kyrandia_profile_Flo | Flo          | 1                        | 1                    | 101                 | zapher                   |

    When Joe performs "learn zapher"
    And Joe performs "cast zapher"
    Then Joe should see "...You don't have the power.\n"
    And Flo should see "***\nJoe is mouthing off.\n"

  @notenough
  Scenario: Casting spell without enough spell points
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | zapher                   |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | zapher                   |
    And Joe has 2 spell points

    When Joe performs "learn zapher"
    And Joe performs "cast zapher"
    Then Joe should see "...You don't have the power.\n"
    And Flo should see "***\nJoe is waving his arms.\n"

  @zapher @protection
  Scenario: Casting spell zapher at protected player
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook | field_kyrandia_prot_lightning |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | zapher                   | 0                             |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | zapher                   | 8                             |

    When Joe performs "learn zapher"
    And Joe performs "cast zapher flo"
    Then Joe should see "...A bolt of lightning flies from you finger but is deflected from Flo.\n"
    And Flo should see "***\nA bolt of lightning flies from Joe's finger but is deflected from you.\n"
    And Moe should see "***\nA bolt of lightning flies from Joe's finger at Flo, but she deflects it with\na flick of the wrist.\n"

  @zapher @mercy
  Scenario: Casting spell zapher at too low level player
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | zapher                   |
      | kyrandia_profile_Flo | Flo          | 1                        | 1                    | 101                 | zapher                   |

    When Joe performs "learn zapher"
    And Joe performs "cast zapher flo"
    Then Joe should see "...Flo is protected by the Goddess Tashanna!  The spell fails!\n"
    And Flo should see "***\nJoe tries to cast a spell on you, but you are protected by the Goddess\nTashanna!\n"
    And Moe should see "***\nJoe tries to cast a spell on Flo, but she is protected by the Goddess\nTashanna!\n"

  @zapher @legit
  Scenario: Casting spell zapher at a valid target
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | zapher                   |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | zapher                   |

    When Joe performs "learn zapher"
    And Joe performs "cast zapher flo"
    Then Joe should see "...A bolt of lightning flies from your finger, striking Flo.\n"
    And Flo should see "***\nA bolt of lightning flies from Joe's finger striking you for 8\ndamage.\n"
    And Moe should see "***\nA bolt of lightning flies from Joe's finger striking Flo\nand she takes some damage.\n"

  @fpandl @protection
  Scenario: Casting spell fpandl at protected player
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook | field_kyrandia_protection_fire |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | fpandl                   | 0                              |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | zapher                   | 8                              |

    When Joe performs "learn fpandl"
    And Joe performs "cast fpandl flo"
    Then Joe should see "...A red bolt flies from your fingers but fails to make contact with Flo.\n"
    And Flo should see "***\nA red bolt flies from Joe's fingers but fails to make contact with you.\n"
    And Moe should see "***\nA red bolt flies from Joe's fingers at Flo but fails to make contact.\n"

  @fpandl @mercy
  Scenario: Casting spell fpandl doesn't have a mercy level (0)
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | fpandl                   |
      | kyrandia_profile_Flo | Flo          | 1                        | 1                    | 101                 | zapher                   |

    When Joe performs "learn fpandl"
    And Joe performs "cast fpandl flo"
    Then Joe should see "...A red bolt flies from your fingers and strikes Flo in a small, bright\nflash.\n"
    And Flo should see "***\nA red bolt flies from Joe's fingers at you and strikes you in a small flash\ncausing 4 points of damage.\n"
    And Moe should see "***\nA red bolt flies from Joe's fingers at Flo and strikes in a small flash\nand she loses some strength.\n"

  @fpandl @legit
  Scenario: Casting spell fpandl at a valid target
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | fpandl                   |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | zapher                   |

    When Joe performs "learn fpandl"
    And Joe performs "cast fpandl flo"
    Then Joe should see "...A red bolt flies from your fingers and strikes Flo in a small, bright\nflash.\n"
    And Flo should see "***\nA red bolt flies from Joe's fingers at you and strikes you in a small flash\ncausing 4 points of damage.\n"
    And Moe should see "***\nA red bolt flies from Joe's fingers at Flo and strikes in a small flash\nand she loses some strength.\n"

  @frostie @protection
  Scenario: Casting spell frostie at protected player
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook | field_kyrandia_protection_ice |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | frostie                  | 0                             |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | zapher                   | 8                             |

    When Joe performs "learn frostie"
    And Joe performs "cast frostie flo"
    Then Joe should see "...A dense blue mist leaves your palms at Flo but does not seem to\nhave any effects.\n"
    And Flo should see "***\nA dense blue mist leaves Joe's palms towards you but does not seem to\nhave any effects.\n"
    And Moe should see "***\nA dense blue mist leaves Joe's palms towards Flo but does not\nseem to have any effects.\n"

  @frostie @mercy
  Scenario: Casting spell frostie under the mercy level (1)
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | frostie                  |
      | kyrandia_profile_Flo | Flo          | 1                        | 1                    | 101                 | zapher                   |

    When Joe performs "learn frostie"
    And Joe performs "cast frostie flo"
    Then Joe should see "...Flo is protected by the Goddess Tashanna!  The spell fails!\n"
    And Flo should see "***\nJoe tries to cast a spell on you, but you are protected by the Goddess\nTashanna!\n"
    And Moe should see "***\nJoe tries to cast a spell on Flo, but she is protected by the Goddess\nTashanna!\n"

  @frostie @legit
  Scenario: Casting spell frostie at a valid target
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | frostie                  |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | zapher                   |

    When Joe performs "learn frostie"
    And Joe performs "cast frostie flo"
    Then Joe should see "...A dense blue mist leaves your palms and surrounds Flo in a\ncone of cold air.\n"
    And Flo should see "***\nA dense blue mist leaves Joe's palms and surrounds you in a cone\nof cold air causing you 16 points of damage.\n"
    And Moe should see "***\nA dense blue mist leaves Joe's palms and surrounds Flo in a cone\nof cold air and she sustains some damage.\n"

  @frythes @protection
  Scenario: Casting spell frythes at protected player
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook | field_kyrandia_protection_fire |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | frythes                  | 0                             |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | zapher                   | 8                             |

    When Joe performs "learn frythes"
    And Joe performs "cast frythes flo"
    Then Joe should see "...An enormous red bolt flies from your fingers but fails to make contact\nwith Flo.\n"
    And Flo should see "***\nAn enormous red bolt flies from Joe's fingers but fails to make contact\nwith you.\n"
    And Moe should see "***\nAn enormous red bolt flies from Joe's fingers at Flo but fails to\nmake contact.\n"

  @frythes @mercy
  Scenario: Casting spell frythes under the mercy level (1)
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | frythes                  |
      | kyrandia_profile_Flo | Flo          | 1                        | 1                    | 101                 | zapher                   |

    When Joe performs "learn frythes"
    And Joe performs "cast frythes flo"
    Then Joe should see "...Flo is protected by the Goddess Tashanna!  The spell fails!\n"
    And Flo should see "***\nJoe tries to cast a spell on you, but you are protected by the Goddess\nTashanna!\n"
    And Moe should see "***\nJoe tries to cast a spell on Flo, but she is protected by the Goddess\nTashanna!\n"

  @frythes @legit
  Scenario: Casting spell frythes at a valid target
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | frythes                  |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | zapher                   |

    When Joe performs "learn frythes"
    And Joe performs "cast frythes flo"
    Then Joe should see "...An enormous red bolt flies from your fingers and strikes Flo in\nblinding flash.\n"
    And Flo should see "***\nAn enormous red bolt flies from Joe's fingers and you and strikes you in\na blinding flash causing 22 points of damage.\n"
    And Moe should see "***\nAn enormous red bolt flies from Joe's fingers at Flo and striker in a\nblinding flash and she loses a great deal strength.\n"

  @gotcha @protection
  Scenario: Casting spell gotcha at protected player
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook | field_kyrandia_prot_lightning |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | gotcha                  | 0                             |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | zapher                   | 8                             |

    When Joe performs "learn gotcha"
    And Joe performs "cast gotcha flo"
    Then Joe should see "...A powerful bolt of lightning flies from your fingers but is deflected\nfrom Flo.\n"
    And Flo should see "***\nA power bolt of lightning flies from Joe's finger but is deflected from you.\n"
    And Moe should see "***\nA powerful bolt of lightning flies from Joe's finger at Flo but is deflected.\n"

  @gotcha @mercy
  Scenario: Casting spell gotcha under the mercy level
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | gotcha                  |
      | kyrandia_profile_Flo | Flo          | 1                        | 1                    | 101                 | zapher                   |

    When Joe performs "learn gotcha"
    And Joe performs "cast gotcha flo"
    Then Joe should see "...Flo is protected by the Goddess Tashanna!  The spell fails!\n"
    And Flo should see "***\nJoe tries to cast a spell on you, but you are protected by the Goddess\nTashanna!\n"
    And Moe should see "***\nJoe tries to cast a spell on Flo, but she is protected by the Goddess\nTashanna!\n"

  @gotcha @legit
  Scenario: Casting spell gotcha at a valid target
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | gotcha                  |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | zapher                   |

    When Joe performs "learn gotcha"
    And Joe performs "cast gotcha flo"
    Then Joe should see "...A powerful bolt of lightning flies from your finger striking Flo.\n"
    And Flo should see "***\nA powerful bolt of lightning flies from Joe's finger striking you for 18\ndamage.\n"
    And Moe should see "***\nA powerful bolt of lightning flies from Joe's finger striking Flo\nand she takes some damage.\n"
