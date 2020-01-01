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

  @weewillo @willowsip
  Scenario: Casting weewillo, willowisp shape
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | pearl           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 206   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook | field_kyrandia_protection_ice |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | weewillo                 | 0                             |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | weewillo                 | 8                             |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | weewillo                 | 0                             |

    When Joe performs "learn weewillo"
    And Joe performs "cast weewillo"

    Then Joe should see "...As you cast the spell, you are suddenly transformed into a willowisp with\nwings!\n"
    And Flo should see "***\nJoe suddenly transforms into a willowisp with wings!\n"

    When Joe performs "move south"
    And Flo should see "***\nSome willowisp has just moved off to the south!\n"
    And Moe should see "***\nSome willowisp has just appeared from the north!\n"

    When Moe performs "look joe"
    Then Moe should see "...You're in a golden forest. You are surrounded by tall, shimmering golden elm trees, their breathtaking golden branches and leaves scintillating in the sunlight. Although formed of solid gold, the trees still appear to be living in full bloom, swaying enchantingly in the gentle breezes of this wonderful paradise. A feeling of eternal love swells within your soul, as though this is the heaven you've always wished to spend your life in forever. The trees blocks most of your view with their sparkling beauty, but you can move off in any direction."
    And Moe should see "Some willowisp is here."

    When Moe performs "look willowisp"
    Then Moe should see "...The willowisp quickly flies from your direct vision.\n"
    And Joe should see "***\nMoe is looking at you carefully.\n"


  @weewillo @willowsip @fly @chasm
  Scenario: Casting weewillo, willowisp shape, flying
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 179   | pearl           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 179   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 180   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook | field_kyrandia_protection_ice |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | weewillo                 | 0                             |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | weewillo                 | 8                             |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | weewillo                 | 0                             |

    When Joe performs "learn weewillo"
    And Joe performs "cast weewillo"
    And Joe performs "fly"
    Then Joe should see
    """
    ...As you lift above the ground, you feel yourself gracefully fly across the
    endless depths of the chasm to the other side...

    """

    And Flo should see "***\nSome willowisp has just gracefully flown across the chasm!\n"
    And Moe should see "***\nSome willowisp has just gracefully flown from across the chasm!\n"

    And Joe should be in "Location 180"

    And Joe performs "fly"
    Then Joe should see
    """
    ...As you lift above the ground, you feel yourself gracefully fly across the
    endless depths of the chasm to the other side...

    """

    And Moe should see "***\nSome willowisp has just gracefully flown across the chasm!\n"
    And Flo should see "***\nSome willowisp has just gracefully flown from across the chasm!\n"

    And Joe should be in "Location 179"

  @weewillo @willowsip @fly @nofly
  Scenario: Casting weewillo, willowisp shape, flying
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 144   | pearl           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 144   | rose            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook | field_kyrandia_protection_ice |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | weewillo                 | 0                             |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | weewillo                 | 8                             |

    When Joe performs "learn weewillo"
    And Joe performs "cast weewillo"
    And Joe performs "fly"

    Then Joe should see "...For some mysterious reason, you don't really feel like flying here.\n"
    And Flo should see "***\nSome willowisp is attempting to fly, without much success.\n"
    And Joe should be in "Location 144"

  @flyaway @pegasus
  Scenario: Casting flyaway, pegasus shape
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | pearl           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 206   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook | field_kyrandia_protection_ice |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | flyaway                  | 0                             |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | flyaway                  | 8                             |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | flyaway                  | 0                             |

    When Joe performs "learn flyaway"
    And Joe performs "cast flyaway"

    Then Joe should see "...Suddenly, you are transformed into a beautiful, white pegasus!\n"
    And Flo should see "***\nJoe suddenly transforms into a beautiful, white pegasus!\n"

    When Joe performs "move south"
    And Flo should see "***\nSome pegasus has just moved off to the south!\n"
    And Moe should see "***\nSome pegasus has just appeared from the north!\n"

    When Moe performs "look joe"
    Then Moe should see "...You're in a golden forest. You are surrounded by tall, shimmering golden elm trees, their breathtaking golden branches and leaves scintillating in the sunlight. Although formed of solid gold, the trees still appear to be living in full bloom, swaying enchantingly in the gentle breezes of this wonderful paradise. A feeling of eternal love swells within your soul, as though this is the heaven you've always wished to spend your life in forever. The trees blocks most of your view with their sparkling beauty, but you can move off in any direction."
    And Moe should see "Some pegasus is here."

    When Moe performs "look pegasus"
    Then Moe should see "...The pegasus is a wonderful creature, but it moves quickly away from your\ndirect vision.\n"
    And Joe should see "***\nMoe is looking at you carefully.\n"

  @flyaway @pegasus @fly @chasm
  Scenario: Casting flyaway, pegasus shape, flying
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 22    | pearl           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 22    | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 189   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook | field_kyrandia_protection_ice |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | flyaway                  | 0                             |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | flyaway                  | 8                             |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | flyaway                  | 0                             |

    When Joe performs "learn flyaway"
    And Joe performs "cast flyaway"
    And Joe performs "fly"
    Then Joe should see
    """
    ...As you majestically soar into the air, you feel yourself magically
    propelled across the beautiful, sparkling sea...

    """

    And Flo should see "***\nSome pegasus has just majestically flown across the sea!\n"
    And Moe should see "***\nSome pegasus has just majestically flown from across the sea!\n"

    And Joe should be in "Location 189"

    And Joe performs "fly"
    Then Joe should see
    """
    ...As you majestically soar into the air, you feel yourself magically
    propelled across the beautiful, sparkling sea...

    """

    And Moe should see "***\nSome pegasus has just majestically flown across the sea!\n"
    And Flo should see "***\nSome pegasus has just majestically flown from across the sea!\n"

    And Joe should be in "Location 22"

  @flyaway @pegasus @fly @nofly
  Scenario: Casting flyaway, pegasus shape, flying
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 144   | pearl           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 144   | rose            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook | field_kyrandia_protection_ice |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | flyaway                  | 0                             |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | flyaway                  | 8                             |

    When Joe performs "learn flyaway"
    And Joe performs "cast flyaway"
    And Joe performs "fly"

    Then Joe should see "...For some mysterious reason, you don't really feel like flying here.\n"
    And Flo should see "***\nSome pegasus is attempting to fly, without much success.\n"
    And Joe should be in "Location 144"

  @gringri @pseudodragon
  Scenario: Casting gringri, pegasus shape
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | pearl           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 206   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook | field_kyrandia_protection_ice |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | gringri                  | 0                             |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | gringri                  | 8                             |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | gringri                  | 0                             |

    When Joe performs "learn gringri"
    And Joe performs "cast gringri"

    Then Joe should see "...Suddenly, you are transformed into a ugly, puesdo dragon!\n"
    And Flo should see "***\nJoe suddenly transforms into a ugly puesdo dragon!\n"

    When Joe performs "move south"
    And Flo should see "***\nSome psuedo dragon has just moved off to the south!\n"
    And Moe should see "***\nSome psuedo dragon has just appeared from the north!\n"

    When Moe performs "look joe"
    Then Moe should see "...You're in a golden forest. You are surrounded by tall, shimmering golden elm trees, their breathtaking golden branches and leaves scintillating in the sunlight. Although formed of solid gold, the trees still appear to be living in full bloom, swaying enchantingly in the gentle breezes of this wonderful paradise. A feeling of eternal love swells within your soul, as though this is the heaven you've always wished to spend your life in forever. The trees blocks most of your view with their sparkling beauty, but you can move off in any direction."
    And Moe should see "Some psuedo dragon is here."

    When Moe performs "look psuedo dragon"
    Then Moe should see "...The pseudo dragon is so ugly, that you turn your head immediately.\n"
    And Joe should see "***\nMoe is looking at you carefully.\n"

  @gringri @pseudodragon @fly @nofly
  Scenario: Casting gringri, pseudodragon shape, flying
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 144   | pearl           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 144   | rose            | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook | field_kyrandia_protection_ice |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | gringri                  | 0                             |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | gringri                  | 8                             |

    When Joe performs "learn gringri"
    And Joe performs "cast gringri"
    And Joe performs "fly"

    Then Joe should see "...For some mysterious reason, you don't really feel like flying here.\n"
    And Flo should see "***\nSome psuedo dragon is attempting to fly, without much success.\n"
    And Joe should be in "Location 144"

  @cantcmeha @invisible
  Scenario: Casting cantcmeha, invisible shape
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | pearl           | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose            | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 206   | rose            | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook | field_kyrandia_protection_ice |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | cantcmeha                | 0                             |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha                | 8                             |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | cantcmeha                | 0                             |

    When Joe performs "learn cantcmeha"
    And Joe performs "cast cantcmeha"

    Then Joe should see "...You see yourself fading away, and soon you are completely invisible!\n"
    And Flo should see "***\nJoe fades away and becomes invisible!\n"

    When Joe performs "move south"
    And Flo should see "***\nSome Unseen Force has just moved off to the south!\n"
    And Moe should see "***\nSome Unseen Force has just appeared from the north!\n"

    When Moe performs "look joe"
    Then Moe should see "...You're in a golden forest. You are surrounded by tall, shimmering golden elm trees, their breathtaking golden branches and leaves scintillating in the sunlight. Although formed of solid gold, the trees still appear to be living in full bloom, swaying enchantingly in the gentle breezes of this wonderful paradise. A feeling of eternal love swells within your soul, as though this is the heaven you've always wished to spend your life in forever. The trees blocks most of your view with their sparkling beauty, but you can move off in any direction."
    And Moe should see "Some Unseen Force is here."

    When Moe performs "look unseen force"
    Then Moe should see "...The force is unseen!\n"
    And Joe should see "***\nMoe is looking at you carefully.\n"

  @clutzopho
  Scenario: Casting clutzopho
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | pearl                        | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | clutzopho                |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha                |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | cantcmeha                |

    When Joe performs "learn clutzopho"
    And Joe performs "cast clutzopho flo"
    Then Joe should see "...You successfully cast the spell and a yellow ball of light flies from your\nfingers.\n"
    And Flo should see "***\nJoe successfully casts a spell and a yellow ball of light flies at you.\n"
    And Moe should see "***\nJoe successfully casts a spell and a yellow ball of light flies at Flo.\n"
    And Flo should see "***\nYou just dropped your rose.\n"
    And Flo should see "***\nYou just dropped your garnet.\n"
    And Flo should see "***\nYou just dropped your diamond.\n"
    And Flo should see "***\nYou just dropped your wand.\n"
    And Flo should see "***\nYou just dropped your key.\n"

    And Flo should not have rose in inventory
    And Flo should not have garnet in inventory
    And Flo should not have diamond in inventory
    And Flo should not have wand in inventory
    And Flo should not have key in inventory

    And Joe should see "***\nFlo has just dropped her rose.\n"
    And Joe should see "***\nFlo has just dropped her garnet.\n"
    And Joe should see "***\nFlo has just dropped her diamond.\n"
    And Joe should see "***\nFlo has just dropped her wand.\n"
    And Joe should see "***\nFlo has just dropped her key.\n"

    And Moe should see "***\nFlo has just dropped her rose.\n"
    And Moe should see "***\nFlo has just dropped her garnet.\n"
    And Moe should see "***\nFlo has just dropped her diamond.\n"
    And Moe should see "***\nFlo has just dropped her wand.\n"
    And Moe should see "***\nFlo has just dropped her key.\n"

  @clutzopho
  Scenario: Casting clutzopho
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | pearl                        | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | clutzopho                | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha                | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | cantcmeha                | 0                               |

    When Joe performs "learn clutzopho"
    And Joe performs "cast clutzopho flo"
    Then Joe should not see "...You successfully cast the spell and a yellow ball of light flies from your\nfingers.\n"
    And Flo should not see "***\nJoe successfully casts a spell and a yellow ball of light flies at you.\n"
    And Moe should not see "***\nJoe successfully casts a spell and a yellow ball of light flies at Flo.\n"
    And Flo should not see "***\nYou just dropped your rose.\n"
    And Flo should not see "***\nYou just dropped your garnet.\n"
    And Flo should not see "***\nYou just dropped your diamond.\n"
    And Flo should not see "***\nYou just dropped your wand.\n"
    And Flo should not see "***\nYou just dropped your key.\n"

    And Flo should have rose in inventory
    And Flo should have garnet in inventory
    And Flo should have diamond in inventory
    And Flo should have wand in inventory
    And Flo should have key in inventory

    And Joe should not see "***\nFlo has just dropped her rose.\n"
    And Joe should not see "***\nFlo has just dropped her garnet.\n"
    And Joe should not see "***\nFlo has just dropped her diamond.\n"
    And Joe should not see "***\nFlo has just dropped her wand.\n"
    And Joe should not see "***\nFlo has just dropped her key.\n"

    And Moe should not see "***\nFlo has just dropped her rose.\n"
    And Moe should not see "***\nFlo has just dropped her garnet.\n"
    And Moe should not see "***\nFlo has just dropped her diamond.\n"
    And Moe should not see "***\nFlo has just dropped her wand.\n"
    And Moe should not see "***\nFlo has just dropped her key.\n"

    And Joe should see "...You cast the spell accurately, unfortunately, for some mysterious reason\nthe spell does not work.\n"
    And Flo should see "***\nJoe casts a spell, to no avail!\n"
    And Moe should see "***\nJoe casts a spell, to no avail!\n"

  @cuseme
  Scenario: Casting cuseme
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | pearl                        | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | cuseme                   | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha                | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | cantcmeha                | 0                               |

    When Joe performs "learn cuseme"
    And Joe performs "cast cuseme flo"
    Then Joe should see "...As you finish the spell a white mist appears around Flo and the number 50\nappears in your mind.\n"
    And Flo should see "***\nJoe casts a spell and a white mist appears around you for a brief moment.\n"
    And Moe should see "***\nJoe has just cast a spell and a white mist appears around Flo for a brief\nmoment.\n"

  @dumdum
  Scenario: Casting dumdum
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | pearl                        | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | dumdum                           |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha,zapher,weewillo,smokey |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | cantcmeha                        |

    And Flo performs "learn weewillo"
    And Flo performs "learn zapher"
    And Flo performs "learn cantcmeha"
    And Flo performs "learn zapher"
    Then Flo should have weewillo memorized
    And Flo should have zapher memorized
    And Flo should have cantcmeha memorized

    When Joe performs "learn dumdum"
    And Joe performs "cast dumdum flo"
    Then Joe should see "...You cast the spell and your opponent suddenly forgets all the spells\nthat they had memorized.\n"
    And Flo should see "***\nJoe casts a spell at you and suddenly you have forgotten all the spells that\nyou had memorized.\n"
    And Moe should see "***\nJoe casts a spell which leaves Flo scratching around for something that they\nhave forgotten.\n"
    Then Flo should not have weewillo memorized
    And Flo should not have zapher memorized
    And Flo should not have cantcmeha memorized

  @dumdum
  Scenario: Casting dumdum on protected player
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 213   | pearl                        | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 213   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 213   | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | dumdum                           | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha,zapher,weewillo,smokey | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | cantcmeha                        | 0                               |

    And Flo performs "learn weewillo"
    And Flo performs "learn zapher"
    And Flo performs "learn cantcmeha"
    And Flo performs "learn zapher"
    Then Flo should have weewillo memorized
    And Flo should have zapher memorized
    And Flo should have cantcmeha memorized

    When Joe performs "learn dumdum"
    And Joe performs "cast dumdum flo"
    Then Joe should not see "...You cast the spell and your opponent suddenly forgets all the spells\nthat they had memorized.\n"
    And Flo should not see "***\nJoe casts a spell at you and suddenly you have forgotten all the spells that\nyou had memorized.\n"
    And Moe should not see "***\nJoe casts a spell which leaves Flo scratching around for something that they\nhave forgotten.\n"
    Then Flo should have weewillo memorized
    And Flo should have zapher memorized
    And Flo should have cantcmeha memorized

    But Joe should see "...You cast the spell but it doesn't seem to have any effects.\n"
    And Flo should see "***\nJoe casts a spell at you but it doesn't seem to have any effects.\n"
    And Moe should see "***\nJoe casts a spell at Flo but it doesn't seem to have any effects.\n"

  @feeluck
  Scenario: Casting feeluck
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 300   | pearl                        | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 300   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 66    | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | feeluck                          | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha,zapher,weewillo,smokey | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | cantcmeha                        | 0                               |
    And the Kyrandia random number will generate 66

    When Joe performs "learn feeluck"
    And Joe performs "cast feeluck"

    Then Joe should see
    """
    ...Upon completing the required incantation, you are suddenly are blinded by
    a brilliant flash of light.
    ***
    When the light dies out you notice...

    """
    And Flo should see "***\nJoe completes a spell incantation and is suddenly enclosed in a blinding flash\nof light.\n"
    And Flo should see "***\nJoe has just vanished in a blue light!\n"
    And Joe should be in "Location 66"
    And Moe should see "***\nJoe has just appeared in a blue!\n"

  @goto
  Scenario: Casting goto without a target
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 300   | pearl                        | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 300   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 66    | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | goto                             | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha,zapher,weewillo,smokey | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | cantcmeha                        | 0                               |

    When Joe performs "learn goto"
    And Joe performs "cast goto"

    Then Joe should see "...WHAT?!\n"
    And Flo should see "***\nJoe is failing at spellcasting.\n"

  @goto
  Scenario: Casting goto with an invalid target
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 300   | pearl                        | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 300   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 66    | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | goto                             | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha,zapher,weewillo,smokey | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | cantcmeha                        | 0                               |

    When Joe performs "learn goto"
    And Joe performs "cast goto 300"

    Then Joe should see "...You cast the spell successfully but the magical forces of the Goddess\nTashanna do not permit you to travel to that place!\n"
    And Flo should see "***\nJoe casts a spell and looks as though he might be taking a trip but\nthe Goddess Tashanna does not permit it.\n"
    And Joe should be in "Location 300"

  @goto
  Scenario: Casting goto with a valid target
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 300   | pearl                        | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 300   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 66    | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | goto                             | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha,zapher,weewillo,smokey | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | cantcmeha                        | 0                               |

    When Joe performs "learn goto"
    And Joe performs "cast goto 66"

    Then Joe should see "...You cast the spell successfully and a sudden dust cloud sucks you up!\n***\nYou feel as though you are traveling through time and space when suddenly\nthe cloud disappears...\n"
    And Flo should see "***\nJoe casts a spell successfully and is suddenly engulfed in a dust cloud.\n"
    And Flo should see "***\nJoe has just vanished in a red cloud!\n"
    And Joe should be in "Location 66"
    And Moe should see "***\nJoe has just appeared in a red cloud!\n"

  @hocus
  Scenario: Casting hocus without bloodstone
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 300   | pearl                        | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 300   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 300   | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | hocus                            | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha,zapher,weewillo,smokey | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | cantcmeha                        | 0                               |

    When Joe performs "learn hocus"
    And Joe performs "cast hocus flo"

    Then Joe should see
    """
    ...You concentrate on the spell and start to cast it.  The power of the spell
    builds but suddenly fizzles out as though something were still missing.

    """
    And Flo should see "***\nJoe is concentrating on casting a spell that suddenly fizzles out as though\nsomething were still missing.\n"
    And Moe should see "***\nJoe is concentrating on casting a spell that suddenly fizzles out as though\nsomething were still missing.\n"

  @hocus
  Scenario: Casting hocus with bloodstone
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 300   | bloodstone                   | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 300   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 300   | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | hocus                            | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha,zapher,weewillo,smokey | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | cantcmeha                        | 0                               |

    When Joe performs "learn hocus"
    And Joe performs "cast hocus flo"

    Then Joe should see
    """
    ...Suddenly your bloodstone starts to vibrate wildly.
    ***
    The bloodstone then mysteriously vanishes.
    ***
    Flo starts to shake violently in a fit of delusion.
    ***
    Flo's fit subsides and she looks quite defenseless.

    """
    And Flo should see
    """
    ***
    Suddenly the bloodstone Joe is holding starts to vibrate wildly.
    ***
    The bloodstone then mysteriously vanishes.
    ***
    You suddenly start to feel very ill as though something has invaded your body.
    ***
    The feeling suddenly vanishes, leaving you feel completely defenseless.

    """
    And Moe should see
    """
    ***
    Flo suddenly looks like she is having problems, controlling things.
    ***
    In the midst of the confusion, Flo suddenly looks very ill.
    ***
    Just as mysteriously as things started, things are now quite, but Flo looks
    completely defenseless.

    """
    And Joe should not have bloodstone in inventory

  @howru
  Scenario: Casting howru
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 300   | bloodstone                   | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 300   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 300   | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | howru                            | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha,zapher,weewillo,smokey | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | cantcmeha                        | 0                               |

    When Joe performs "learn howru"
    And Joe performs "cast howru flo"
    Then Joe should see "...As the spell is cast a silver ball appears by your target.\n***\nThe ball disappears and the number 100 appears in your mind.\n"
    And Flo should see "***\nAs Joe casts a spell, a small silver ball appears by your head and then\ndisappears.\n"
    And Moe should see "***\nAs Joe casts a spell, a small silver ball appears by Flo's head\nand then disappears.\n"

  @ibebad
  Scenario: Casting ibebad without sapphire
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 300   | pearl                        | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 300   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 300   | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | ibebad                           | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha,zapher,weewillo,smokey | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | cantcmeha                        | 0                               |

    When Joe performs "learn ibebad"
    And Joe performs "cast ibebad"

    Then Joe should see
    """
    ...You concentrate on the spell and start to cast it.  The power of the spell
    builds but suddenly fizzles out as though something were still missing.

    """
    And Flo should see "***\nJoe is concentrating on casting a spell that suddenly fizzles out as though\nsomething were still missing.\n"
    And Moe should see "***\nJoe is concentrating on casting a spell that suddenly fizzles out as though\nsomething were still missing.\n"

  @ibebad
  Scenario: Casting ibebad with sapphire
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 300   | sapphire                     | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 300   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 300   | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | ibebad                           | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha,zapher,weewillo,smokey | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | cantcmeha                        | 0                               |

    When Joe performs "learn ibebad"
    And Joe performs "cast ibebad"

    Then Joe should see
    """
    ...Upon completing the incantation of this spell, your sapphire starts to
    glow in a dark shade of blue and starts to burn your hand with intolerable
    pain.
    ***
    The gem suddenly vanishes and as you look at your hand you notice that it looks
    perfectly ok.
    ***
    You have the feeling of being completely impenetrable.

    """
    And Flo should see
    """
    ***
    One of Joe's possessions starts to glow in a dark shade of blue.
    ***
    Joe seems to be experiencing some sort of discomfort with one of his hands.
    ***
    Joe is looking like nothing can every hurt him again.

    """
    And Moe should see
    """
    ***
    One of Joe's possessions starts to glow in a dark shade of blue.
    ***
    Joe seems to be experiencing some sort of discomfort with one of his hands.
    ***
    Joe is looking like nothing can every hurt him again.

    """
    And Joe should not have sapphire in inventory

  @mower
  Scenario: Casting mower
    Given location content:
      | title        | body                    | field_visible_items | field_object_location |
      | Janet's Void | You're in Janet's Void. | dragonstaff,pearl   | in the void           |
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Janet's Void   | sapphire                     | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Janet's Void   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Janet's Void   | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | mower                            | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha,zapher,weewillo,smokey | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 25                   | cantcmeha                        | 0                               |

    And Joe performs "drop sapphire"
    And Flo performs "drop rose"
    And Flo performs "drop garnet"
    And Flo performs "drop diamond"

    When Joe performs "learn mower"
    And Joe performs "cast mower"
    Then Joe should see "...You cast the spell!\n"
    And Joe should see "***\nThe sapphire in the void vanishes!\n"
    And Joe should see "***\nThe rose in the void vanishes!\n"
    And Joe should see "***\nThe garnet in the void vanishes!\n"
    And Joe should see "***\nThe diamond in the void vanishes!\n"
    And Joe should see "***\nThe dragonstaff in the void vanishes!\n"
    And Joe should see "***\nThe pearl in the void vanishes!\n"

    And Flo should see "***\nThe sapphire in the void vanishes!\n"
    And Flo should see "***\nThe rose in the void vanishes!\n"
    And Flo should see "***\nThe garnet in the void vanishes!\n"
    And Flo should see "***\nThe diamond in the void vanishes!\n"
    And Flo should see "***\nThe dragonstaff in the void vanishes!\n"
    And Flo should see "***\nThe pearl in the void vanishes!\n"

  @takethat
  Scenario: Casting takethat on protected target
    Given location content:
      | title        | body                    | field_visible_items | field_object_location |
      | Janet's Void | You're in Janet's Void. | dragonstaff,pearl   | in the void           |
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Janet's Void   | sapphire                     | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Janet's Void   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Janet's Void   | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | takethat                         | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha,zapher,weewillo,smokey | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 1                    | cantcmeha                        | 0                               |
    And Flo should have 50 spell points

    When Joe performs "learn takethat"
    And Joe performs "cast takethat flo"
    Then Joe should see "...You cast the spell but a strange force prevents it from working.\n"
    And Flo should see "***\nJoe casts a spell on you but a strange force prevents it from working.\n"
    And Moe should see "***\nJoe casts a spell on Flo but a strange force prevents it from working.\n"
    And Flo should have 50 spell points

  @takethat
  Scenario: Casting takethat on valid target
    Given location content:
      | title        | body                    | field_visible_items | field_object_location |
      | Janet's Void | You're in Janet's Void. | dragonstaff,pearl   | in the void           |
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Janet's Void   | sapphire                     | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Janet's Void   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Janet's Void   | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | takethat                         |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha,zapher,weewillo,smokey |
      | kyrandia_profile_Moe | Moe          | 0                        | 1                    | cantcmeha                        |
    And Flo should have 50 spell points

    When Joe performs "learn takethat"
    And Joe performs "cast takethat flo"
    Then Joe should see "...You cast the spell successfully, causing your target to lose some power.\n"
    And Flo should see "***\nJoe casts a spell on you, draining you of some of your magical power.\n"
    And Moe should see "***\nJoe casts a spell on Flo, draining some magical power.\n"
    And Flo should have 42 spell points

  @saywhat
  Scenario: Casting saywhat on protected target
    Given location content:
      | title        | body                    | field_visible_items | field_object_location |
      | Janet's Void | You're in Janet's Void. | dragonstaff,pearl   | in the void           |
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Janet's Void   | sapphire                     | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Janet's Void   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Janet's Void   | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | saywhat                          | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha,zapher,weewillo,smokey | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 1                    | cantcmeha                        | 0                               |
    And Flo should have 50 spell points
    And Flo performs "learn zapher"
    And Flo performs "learn weewillo"
    Then Flo should have zapher memorized
    And flo should have weewillo memorized

    When Joe performs "learn saywhat"
    And Joe performs "cast saywhat flo"
    Then Joe should see "...You cast the spell but it doesn't seem to have any effects.\n"
    And Flo should see "***\nJoe casts a spell at you but it doesn't seem to have any effects.\n"
    And Moe should see "***\nJoe casts a spell at Flo but it doesn't seem to have any effects.\n"
    And Flo should have 50 spell points
    And Flo should have zapher memorized
    And Flo should have weewillo memorized

  @saywhat
  Scenario: Casting saywhat on valid target
    Given location content:
      | title        | body                    | field_visible_items | field_object_location |
      | Janet's Void | You're in Janet's Void. | dragonstaff,pearl   | in the void           |
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Janet's Void   | sapphire                     | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Janet's Void   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Janet's Void   | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | saywhat                          |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha,zapher,weewillo,smokey |
      | kyrandia_profile_Moe | Moe          | 0                        | 1                    | cantcmeha                        |
    And Flo should have 50 spell points
    And Flo performs "learn zapher"
    And Flo performs "learn weewillo"
    Then Flo should have zapher memorized
    And flo should have weewillo memorized

    When Joe performs "learn saywhat"
    And Joe performs "cast saywhat flo"
    Then Joe should see "...You cast the spell and your opponent forgets a spell.\n"
    And Flo should see "***\nJoe casts a spell at you and suddenly you have forgotten something.\n"
    And Moe should see "***\nJoe casts a spell at Flo and there are no visual effects.\n"
    And Flo should have 50 spell points
    And Flo should have zapher memorized
    But Flo should not have weewillo memorized

  @nosey
  Scenario: Casting nosey on valid target
    Given location content:
      | title        | body                    | field_visible_items | field_object_location |
      | Janet's Void | You're in Janet's Void. | dragonstaff,pearl   | in the void           |
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Janet's Void   | sapphire                     | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Janet's Void   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Janet's Void   | rose                         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | nosey                            |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha,zapher,weewillo,smokey |
      | kyrandia_profile_Moe | Moe          | 0                        | 1                    | cantcmeha                        |
    And Flo should have 50 spell points
    And Flo performs "learn zapher"
    And Flo performs "learn smokey"
    And Flo performs "learn weewillo"
    Then Flo should have zapher memorized
    And flo should have weewillo memorized

    When Joe performs "learn nosey"
    And Joe performs "cast nosey flo"
    And Joe should see "...As you cast the spell you feel a strange sensation and realize that\nFlo has zapher, smokey, and weewillo memorized.\n"
    And Flo should see "***\nJoe has just entered your mind and been able to read all your spells.\n"
    And Moe should see "***\nJoe has just a spell that is playing mind games with Flo's head.\n"

    When Joe performs "learn nosey"
    And Joe performs "cast nosey moe"
    And Joe should see "...As you cast the spell you feel a strange sensation and realize that\nMoe has no spells memorized.\n"
    And Moe should see "***\nJoe has just entered your mind and been able to read all your spells.\n"
    And Flo should see "***\nJoe has just a spell that is playing mind games with Moe's head.\n"

  @peepint
  Scenario: Casting peepint
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 212   | sapphire                     | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 66    | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 300   | rose                         | 1            | Moe                | Moe                   |
      | Bo    | kyrandia   | Location 212   | rose                         | 1            | Bo                 | Bo                    |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | peepint                          | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha,zapher,weewillo,smokey | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 1                    | cantcmeha                        | 0                               |

    When Joe performs "learn peepint"
    And Joe performs "cast peepint"
    Then Joe should see "...WHAT?!\n"

    # Flo is protected.
    When Joe performs "learn peepint"
    And Joe performs "cast peepint flo"
    Then Joe should see "...Mysteriously, it fails...\n"

    # Theo isn't real.
    When Joe performs "learn peepint"
    And Joe performs "cast peepint theo"
    Then Joe should see "...Mysteriously, it fails...\n"

    When Joe performs "learn peepint"
    And Joe performs "cast peepint moe"
    Then Joe should see "...A vision enters your mind...\n\n"
    And Joe should see "...You're in the royal kitchen. This rather large room was once used to prepare the great feasts and banquets for the Lady Kyra and her guests. Now, however, it lies barren and deserted, as if some horrible evil had vanquished the good magic that once thrived here. A strong odor of sulfur floats from the northern wall. The Hall of the Throne lies to the west."
    And Joe should see "...The vision ends suddenly...\n"
    And Moe should see "***\nYou suddenly get the feeling that you are being watched!\n"
    And Bo should see "***\nJoe is concentrating in a mystical trance!\n"

  @pickpoc
  Scenario: Casting pickpoc
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 212   | sapphire                     | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 212   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Location 212   | bloodstone,moonstone         | 1            | Moe                | Moe                   |
      | Bo    | kyrandia   | Location 212   |                              | 1            | Bo                 | Bo                    |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | pickpoc                          | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | cantcmeha,zapher,weewillo,smokey | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 1                    | cantcmeha                        | 0                               |

    When Joe performs "learn pickpoc"
    And Joe performs "cast pickpoc flo"
    Then Joe should see "...You cast the spell but some magical resistance prevents it from succeeding.\n"
    And Flo should see "***\nJoe casts a spell and you feel something tugging at you without success.\n"
    And Moe should see "***\nJoe casts a spell at Flo but some magical resistance prevents it from working.\n"

    When Joe performs "learn pickpoc"
    And Joe performs "cast pickpoc bo"
    Then Joe should see "...You cast the spell but some magical resistance prevents it from succeeding.\n"
    And Bo should see "***\nJoe casts a spell and you feel something tugging at you without success.\n"
    And Moe should see "***\nJoe casts a spell at Bo but some magical resistance prevents it from working.\n"

    When Joe performs "learn pickpoc"
    And Joe performs "cast pickpoc moe"
    Then Joe should see
    """
    ...The powers of the spell are released in lightning force.
    ***
    The bloodstone that Moe is holding just vanished!
    ***
    The bloodstone just magically appeared in your hands.

    """
    And Moe should see
    """
    ***
    Joe just cast a spell.
    ***
    The bloodstone that you are holding just vanished into thin air.
    ***
    The bloodstone you just lost, magically appeared in Joe's hand.

    """
    And Flo should see
    """
    ***
    Joe just cast a spell.
    ***
    The bloodstone that Moe is holding just vanished into thin air.
    ***
    The bloodstone just reappeared, magically in Joe's hand.

    """
    And Joe should have bloodstone in inventory
    And Moe should not have bloodstone in inventory

  @tiltowait
  Scenario: Casting tiltowait without rose
    Given location content:
      | title        | body                    | field_visible_items | field_object_location |
      | Janet's Void | You're in Janet's Void. | dragonstaff,pearl   | in the void           |
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Janet's Void   | sapphire                     | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Janet's Void   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Bo    | kyrandia   | Janet's Void   |                              | 1            | Bo                 | Bo                    |
      | Moe   | kyrandia   | Location 212   | bloodstone,moonstone         | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | tiltowait                        | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | tiltowait,zapher,weewillo,smokey | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 1                    | cantcmeha                        | 0                               |

    And "Janet's Void" should have dragonstaff
    And "Janet's Void" should have pearl

    When Joe performs "learn tiltowait"
    And Joe performs "cast tiltowait"
    Then Joe should see "...You concentrate on the spell and start to cast it.  The power of the spell\nbuilds but suddenly fizzles out as though something were still missing.\n"
    And Flo should see "***\nJoe is concentrating on casting a spell that suddenly fizzles out as though\nsomething were still missing.\n"

    When Flo performs "learn tiltowait"
    And Flo performs "cast tiltowait"
    Then Flo should see
    """
    ...You start to concentrate upon the words required to cast this powerful
    spell.  Upon completing its words a silence falls upon everybody.
    ***
    A vision appears within your mind of the Goddess Tashanna, standing almighty
    in her domain, snaring at you for what you have just unleased.
    ***
    You notice that the rose you were holding has just vanished.

    """
    And Flo should not have rose in inventory
    And Joe should see "***\nFlo is in deep concentration while attempting to cast a spell.\n"
    And Joe should see "***\nSuddenly, the ground begins to shake violently and a loud crackling noise is\nheard in the distance.\n"
    And Flo should see "***\nSuddenly, the ground begins to shake violently and a loud crackling noise is\nheard in the distance.\n"
    And Moe should see "***\nSuddenly, the ground begins to shake violently and a loud crackling noise is\nheard in the distance.\n"
    And Bo should see "***\nSuddenly, the ground begins to shake violently and a loud crackling noise is\nheard in the distance.\n"
    And Flo should see "***\nA gaping hole opens in the ground swallowing up any objects that may have been\nthere!\n"
    And Joe should see "***\nA gaping hole opens in the ground swallowing up any objects that may have been\nthere!\n"

    Then Bo should see "***\nYou are mystically protected by the mercy of the Goddess Tashanna!\n"
    And Bo should be in "Janet's Void"
    And Joe should see "***\nBo is mystically protected by the mercy of the Goddess Tashanna!\n"
    And Flo should see "***\nBo is mystically protected by the mercy of the Goddess Tashanna!\n"

    And Joe should see "***\nThe intensity of the earthquake bangs you around for what seems like an\neternity causing you 50 points of damage.\n"
    And Joe should have 50 hit points
    And Flo should see "***\nThe intensity of the earthquake bangs you around for what seems like an\neternity causing you 50 points of damage.\n"
    And Flo should have 50 hit points

    And Joe should see "***\nThe intensity of the earthquake bangs Flo around for what seems to be an\neternity causing her massive damage.\n"
    And Flo should see "***\nThe intensity of the earthquake bangs Joe around for what seems to be an\neternity causing him massive damage.\n"

    Then "Janet's Void" should not have dragonstaff
    And "Janet's Void" should not have pearl

  @whoub
  Scenario: Casting whoub
    Given location content:
      | title        | body                    | field_visible_items | field_object_location |
      | Janet's Void | You're in Janet's Void. | dragonstaff,pearl   | in the void           |
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Janet's Void   | sapphire                     | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Janet's Void   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Janet's Void   |                              | 1            | Moe                | Moe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | whoub                            | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | tiltowait,zapher,weewillo,smokey | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 1                    | cantcmeha                        | 0                               |

    When Flo performs "learn weewillo"
    And Flo performs "cast weewillo"

    And Joe performs "learn whoub"
    And Joe performs "cast whoub willowisp"
    Then Joe should see "...A bright light surrounds the target for a second and a little voice\nwhispers to you that your friend is really Flo.\n"
    And Flo should see "***\nJoe casts a spell which surrounds you in bright lights revealing your true\nidentity.\n"
    And Moe should see "***\nJoe casts a spell which surrounds some willowisp in a bright light for a brief moment.\n"

  @zelastone
  Scenario: Casting zelastone
    Given location content:
      | title          | body                    | field_visible_items | field_object_location |
      | Janet's Void   | You're in Janet's Void. | dragonstaff,pearl   | in the void           |
      | Somewhere else | You're somewhere else.  | amethyst            | laying around         |
    Given player content:
      | title | field_game | field_location | field_inventory              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Janet's Void   | sapphire                     | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Janet's Void   | rose,garnet,diamond,wand,key | 1            | Flo                | Flo                   |
      | Moe   | kyrandia   | Somewhere else |                              | 1            | Moe                | Moe                   |
      | Poe   | kyrandia   | Somewhere else |                              | 1            | Poe                | Poe                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And the "kyrandia_profile" "kyrandia_profile_Moe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_spellbook         | field_kyrandia_protection_other |
      | kyrandia_profile_Joe | Joe          | 0                        | 25                   | zelastone                        | 0                               |
      | kyrandia_profile_Flo | Flo          | 1                        | 25                   | tiltowait,zapher,weewillo,smokey | 8                               |
      | kyrandia_profile_Moe | Moe          | 0                        | 1                    | cantcmeha                        | 0                               |

    And the Kyrandia random number will generate 30

    When Joe performs "learn zelastone"
    And Joe performs "cast zelastone"
    Then Joe should see "...Mysteriously, it fails...\n"

    When Joe performs "learn zelastone"
    And Joe performs "cast zelastone bo"
    Then Joe should see
    """
    ...You put all your concentration into cast this highly deadly spell.
    ***
    Suddenly from out of thin air appears a deadly Aerial Servant.
    ***
    Unable to complete its mission, it turns on you, viciously slashing at you
    for what seems to be an eternity.
    ***
    It suddenly emits an ear piercing screech and vanishes, leaving you to your
    misery.

    """
    And Flo should see
    """
    ***
    Joe is putting all his concentration into casting a spell.
    ***
    Suddenly from out of thin air a Deadly Aerial Servant appears.
    ***
    The servant viciously starts to slash at Joe for what seems to be an eternity.
    ***
    The servant then suddenly emits an ear piercing screech and vanishes, leaving
    Joe in a bloody mess.

    """
    And Joe should have 70 hit points

    When Joe performs "learn zelastone"
    And Joe performs "cast zelastone moe"
    Then Joe should see
    """
    ...You put all your concentration into cast this highly deadly spell.
    ***
    Suddenly from out of thin air appears a Deadly Aerial Servant.  The servant
    flies up to you bows and flies off to its target.
    ***
    A shiver runs down your spine as the servant vanishes from your sight.

    """
    And Flo should see
    """
    ***
    Joe is putting all his concentration into casting a spell.
    ***
    Suddenly from out of thin air a Deadly Aerial Servant appears.  The servant
    bows to the caster and then flies off to find its target.

    """
    And Moe should see
    """
    ***
    Suddenly a large Aerial Servant appears from out of thin air.

    """
    And Moe should see
    """
    ***
    The Aerial Servant shrieks at you in hatred and starts shredding you into
    a bloody mess.
    ***
    The servant then vanishes as quickly as it came.

    """
    And Poe should see
    """
    ***
    The Aerial Servant shrieks at Moe and starts tearing him into a bloody mess.
    ***
    The servant then vanishes as quickly as it came.

    """

    When Joe performs "learn zelastone"
    And Joe performs "cast zelastone flo"
    Then Flo should see
    """
    ***
    Suddenly a large Aerial Servant appears from out of thin air.
    ***
    The servant shrieks at you but is instantly dispelled by a strange magical aura.

    """
    And Joe should see
    """
    ***
    Suddenly a large Aerial Servant appears from out of thin air.
    ***
    The servant shrieks at Flo but is instantly dispelled by a strange magical aura.

    """
