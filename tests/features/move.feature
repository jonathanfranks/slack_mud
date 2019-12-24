@api @kyrandia @move
Feature: Movement

  @north
  Scenario: Moving north from the willow tree
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 0     | garnet          | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 0     | diamond         | 1            | Flo                | Flo                   |
      | Mo    | kyrandia   | Location 1     | moonstone       | 1            | Mo                 | Mo                    |
    And Joe should be in "Location 0"

    When Joe performs "move north"
    Then Joe should be in "Location 1"
    And Joe should see "...You're on a north/south path. Surrounded by tall, ominous pine trees, you're located on a narrow dirt path which twists off to the north and south. Dark forest leads off to the east and west sides of this little road, which extends beyond your vision into dark undergrowth. Somewhere nearby you hear the chirping of a songbird singing a faintly familiar romantic melody. The unmistakable aroma of blooming roses also hangs in the air, although you cannot see its origin."
    And Flo should see "***\nJoe has just moved off to the north!\n"
    And Mo should see "***\nJoe has just appeared from the south!\n"
