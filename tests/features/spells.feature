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
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | frythes                  | 0                              |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | zapher                   | 8                              |

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
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | gotcha                   | 0                             |
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
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | gotcha                   |
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
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | gotcha                   |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | zapher                   |

    When Joe performs "learn gotcha"
    And Joe performs "cast gotcha flo"
    Then Joe should see "...A powerful bolt of lightning flies from your finger striking Flo.\n"
    And Flo should see "***\nA powerful bolt of lightning flies from Joe's finger striking you for 18\ndamage.\n"
    And Moe should see "***\nA powerful bolt of lightning flies from Joe's finger striking Flo\nand she takes some damage.\n"

  @blowitawa @protection
  Scenario: Casting spell blowitawa at protected player
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | blowitawa                | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | zapher                   | 8                               |

    When Joe performs "learn blowitawa"
    And Joe performs "cast blowitawa flo"
    Then Joe should see "...You cast the spell with accuracy, but for some mysterious reason, the\nspell is deflected from hitting your target!\n"
    And Flo should see "***\nJoe attempts to cast a spell at you, but it is mystically dispelled by\nyour protection!\n"
    And Moe should see "***\nJoe casts a spell at Flo, but it is mysteriously deflected!\n"
    And Flo should have rose in inventory

  @blowitawa @legit
  Scenario: Casting spell blowitawa at valid player
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | tulip           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | blowitawa                |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | zapher                   |

    When Joe performs "learn blowitawa"
    And Joe performs "cast blowitawa flo"
    Then Joe should see "...You cast the spell, and a mystical beam of orange light flies from your\nfingers, disintegrating Flo's rose!\n"
    And Flo should see "***\nA mystical beam of orange light flies from Joe's fingers, disintegrating\nyou rose!\n"
    And Moe should see "***\nA mystical beam of orange light flies from Joe's fingers at Flo,\ndisintegrating her rose!\n"
    And Flo should not have rose in inventory

  @bookworm @protected
  Scenario: Casting spell bookworm at protected player
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | moonstone       | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook                   | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | bookworm                                   | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | zapher,bookworm,frythes,tiltowait,weewillo | 8                               |

    And Flo performs "learn zapher"
    And Flo performs "learn bookworm"
    And Flo performs "learn tiltowait"

    When Joe performs "learn bookworm"
    And Joe performs "cast bookworm flo"
    Then Flo should have zapher memorized
    And Flo should have bookworm memorized
    And Flo should have tiltowait memorized
    And Flo should have the spell zapher
    And Flo should have the spell bookworm
    And Flo should have the spell tiltowait
    And Joe should see "...A bolt of bright blue lightening flies from your fingers but is magically\ndispelled.\n"
    And Flo should see "***\nA bolt of bright blue lightening flies from Joe's fingers but is magically\ndispelled from affecting you.\n"
    And Moe should see "***\nA bolt of bright blue lightening flies from Joe's fingers at Flo but is\nmagically dispelled.\n"

  @bookworm @nocomponent
  Scenario: Casting spell bookworm at valid player without moonstone
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | garnet          | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook                   |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | bookworm                                   |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | zapher,bookworm,frythes,tiltowait,weewillo |

    And Flo performs "learn zapher"
    And Flo performs "learn bookworm"
    And Flo performs "learn tiltowait"

    When Joe performs "learn bookworm"
    And Joe performs "cast bookworm flo"
    Then Flo should have zapher memorized
    And Flo should have bookworm memorized
    And Flo should have tiltowait memorized
    And Flo should have the spell zapher
    And Flo should have the spell bookworm
    And Flo should have the spell tiltowait
    And Joe should see "...You concentrate on the spell and start to cast it.  The power of the spell\nbuilds but suddenly fizzles out as though something were still missing.\n"
    And Flo should see "***\nJoe is concentrating on casting a spell that suddenly fizzles out as though\nsomething were still missing.\n"
    And Moe should see "***\nJoe is concentrating on casting a spell that suddenly fizzles out as though\nsomething were still missing.\n"

  @bookworm @legit
  Scenario: Casting spell bookworm at valid player
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | moonstone       | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook                   |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | bookworm                                   |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | zapher,bookworm,frythes,tiltowait,weewillo |

    And Flo performs "learn zapher"
    And Flo performs "learn bookworm"
    And Flo performs "learn tiltowait"

    When Joe performs "learn bookworm"
    And Joe performs "cast bookworm flo"
    Then Flo should not have zapher memorized
    And Flo should not have bookworm memorized
    And Flo should not have tiltowait memorized
    And Flo should not have the spell zapher
    And Flo should not have the spell bookworm
    And Flo should not have the spell tiltowait

    And Joe should see
    """
    ...Your moonstone suddenly starts to glow brightly.
    ***
    A bolt of blue lightening flies from the moonstone striking Flo's spellbook
    in a brilliant flash.
    ***
    Flo's spellbook has been totally wiped clean.
    ***
    Your moonstone then suddenly vanishes.

    """

    And Flo should see
    """
    ***
    Joe's moonstone starts to glow strangely.
    ***
    A bolt of blue lightening flashes from Joe striking your spellbook.

    """

    And Moe should see
    """
    ***
    Joe's moonstone starts to glow strangely.
    ***
    A bolt of blue lightening flashes from Joe striking Flo's spellbook.

    """

  @burnup
  Scenario: Casting spell burnup at valid player
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | moonstone       | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | burnup                   |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | weewillo                 |
      | kyrandia_profile_Moe | Moe          | 1                        | 25                   | 101                 | weewillo                 |

    When Joe performs "learn burnup"
    And Joe performs "cast burnup"
    Then Joe should see "...A ball of fire erupts from your hands and culminates in a fiery\nexplosion!\n"
    And Flo should see "***\nA ball of fire erupts from Joe's hands and culminates in a fiery explosion!\n"
    And Moe should see "***\nA ball of fire erupts from Joe's hands and culminates in a fiery explosion!\n"
    And Flo should see "***\nYou have been hit by the fire!\n"
    And Moe should see "***\nYou have been hit by the fire!\n"
    And Flo should see "***\nMoe has been hit by the fire.\n"
    And Moe should see "***\nFlo has been hit by the fire.\n"
    And Flo should not see "***\nJoe has been hit by the fire.\n"
    And Moe should not see "***\nJoe has been hit by the fire.\n"
    And Joe should have 100 hit points
    And Flo should have 90 hit points
    And Moe should have 90 hit points

  @burnup @protected
  Scenario: Casting spell burnup at a valid and a protected player
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | moonstone       | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |
      | Bo    | kyrandia   | Location 213   | rose            | 1            | Bo                 | Bo                    |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook | field_kyrandia_protection_fire |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | burnup                   | 0                              |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | weewillo                 | 8                              |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | weewillo                 | 0                              |

    When Joe performs "learn burnup"
    And Joe performs "cast burnup"
    Then Joe should see "...A ball of fire erupts from your hands and culminates in a fiery\nexplosion!\n"
    And Flo should see "***\nA ball of fire erupts from Joe's hands and culminates in a fiery explosion!\n"
    And Moe should see "***\nA ball of fire erupts from Joe's hands and culminates in a fiery explosion!\n"
    And Flo should not see "***\nYou have been hit by the fire!\n"
    But Flo should see "***\nThe explosion seems to have no effect on you.\n"
    But Flo should see "***\nMoe has been hit by the fire.\n"
    But Flo should not see "***\nJoe has been hit by the fire.\n"
    And Flo should have 100 hit points
    And Joe should have 100 hit points

    And Moe should see "***\nYou have been hit by the fire!\n"
    And Moe should have 90 hit points

    And Bo should see "***\nYou are mystically protected by the mercy of the Goddess Tashanna!\n"
    And Moe should see "***\nBo is mystically protected by the mercy of the Goddess Tashanna!\n"
    And Joe should see "***\nBo is mystically protected by the mercy of the Goddess Tashanna!\n"
    And Flo should see "***\nBo is mystically protected by the mercy of the Goddess Tashanna!\n"
    And Bo should not see "***\nBo is mystically protected by the mercy of the Goddess Tashanna!\n"
    And Bo should have 4 hit points


  @chillou @nopearl
  Scenario: Casting spell chillou at valid player
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | moonstone       | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | chillou                  |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | weewillo                 |
      | kyrandia_profile_Moe | Moe          | 1                        | 25                   | 101                 | weewillo                 |

    When Joe performs "learn chillou"
    And Joe performs "cast chillou"

    And Joe should see "...You concentrate on the spell and start to cast it.  The power of the spell\nbuilds but suddenly fizzles out as though something were still missing.\n"
    And Flo should see "***\nJoe is concentrating on casting a spell that suddenly fizzles out as though\nsomething were still missing.\n"
    And Moe should see "***\nJoe is concentrating on casting a spell that suddenly fizzles out as though\nsomething were still missing.\n"

  @chillou
  Scenario: Casting spell chillou at valid player
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | pearl           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_spellbook |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | 1                   | chillou                  |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | 101                 | weewillo                 |
      | kyrandia_profile_Moe | Moe          | 1                        | 25                   | 101                 | weewillo                 |

    When Joe performs "learn chillou"
    And Joe performs "cast chillou"

    Then Joe should see "...As the last word of this spell leaves your lips, the room starts to darken\nas a terrible storm approaches.\n"
    And Flo should see "***\nAs the final words of a spell leave the lips of Joe, the room starts to darken\nas a terrible storm approaches.\n"
    And Moe should see "***\nAs the final words of a spell leave the lips of Joe, the room starts to darken\nas a terrible storm approaches.\n"

    And Moe should see "***\nLarge chunks of ice fly at you from the storm, striking you in several vital\nareas causing you 30 points of damage.\n"
    And Moe should see "***\nLarge chunks of ice flying from the storm strike Flo in several vital areas\ncausing a great deal of damage.\n"
    And Moe should see "***\nLarge chunks of ice flying from the storm strike Joe in several vital areas\ncausing a great deal of damage.\n"

    And Flo should see "***\nLarge chunks of ice fly at you from the storm, striking you in several vital\nareas causing you 30 points of damage.\n"
    And Flo should see "***\nLarge chunks of ice flying from the storm strike Moe in several vital areas\ncausing a great deal of damage.\n"
    And Flo should see "***\nLarge chunks of ice flying from the storm strike Joe in several vital areas\ncausing a great deal of damage.\n"

    And Joe should see "***\nLarge chunks of ice fly at you from the storm, striking you in several vital\nareas causing you 30 points of damage.\n"
    And Joe should see "***\nLarge chunks of ice flying from the storm strike Moe in several vital areas\ncausing a great deal of damage.\n"
    And Joe should see "***\nLarge chunks of ice flying from the storm strike Flo in several vital areas\ncausing a great deal of damage.\n"

    And Joe should have 70 hit points
    And Flo should have 70 hit points
    And Moe should have 70 hit points

  @chillou @protected
  Scenario: Casting spell chillou at a valid and a protected player
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | pearl           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |
      | Bo    | kyrandia   | Location 213   | rose            | 1            | Bo                 | Bo                    |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook | field_kyrandia_protection_ice |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | chillou                  | 0                             |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | weewillo                 | 8                             |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | weewillo                 | 0                             |

    When Joe performs "learn chillou"
    And Joe performs "cast chillou"
    Then Joe should see "...As the last word of this spell leaves your lips, the room starts to darken\nas a terrible storm approaches.\n"
    And Flo should see "***\nAs the final words of a spell leave the lips of Joe, the room starts to darken\nas a terrible storm approaches.\n"
    And Moe should see "***\nAs the final words of a spell leave the lips of Joe, the room starts to darken\nas a terrible storm approaches.\n"

    And Flo should not see "***\nLarge chunks of ice fly at you from the storm, striking you in several vital\nareas causing you 30 points of damage.\n"
    But Flo should see "***\nLarge chunks of ice flying from the storm at you are magically destroyed\nwithout causing you any harm.\n"
    And Flo should see "***\nLarge chunks of ice flying from the storm strike Moe in several vital areas\ncausing a great deal of damage.\n"
    And Flo should see "***\nLarge chunks of ice flying from the storm strike Joe in several vital areas\ncausing a great deal of damage.\n"
    And Flo should have 100 hit points

    And Joe should have 70 hit points

    And Moe should have 70 hit points

    And Bo should see "***\nYou are mystically protected by the mercy of the Goddess Tashanna!\n"
    And Moe should see "***\nBo is mystically protected by the mercy of the Goddess Tashanna!\n"
    And Joe should see "***\nBo is mystically protected by the mercy of the Goddess Tashanna!\n"
    And Flo should see "***\nBo is mystically protected by the mercy of the Goddess Tashanna!\n"
    And Bo should not see "***\nBo is mystically protected by the mercy of the Goddess Tashanna!\n"
    And Bo should have 4 hit points

  @whereami
  Scenario: Casting spell whereami
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | pearl           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose            | 1            | Moe                | Moe                   |
      | Bo    | kyrandia   | Location 213   | rose            | 1            | Bo                 | Bo                    |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook | field_kyrandia_protection_ice |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | whereami                 | 0                             |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | weewillo                 | 8                             |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | weewillo                 | 0                             |

    When Joe performs "learn whereami"
    And Joe performs "cast whereami"
    Then Joe should see "...You cast this tedious spell and realize your located at coordinate 213.\n"
    And Flo should see "***\nJoe is casting a spell to help get his bearings straight.\n"
