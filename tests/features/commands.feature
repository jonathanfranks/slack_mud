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
