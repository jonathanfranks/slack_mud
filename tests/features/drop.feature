@api @drop
Feature: Kyrandia drop commands

  @nothing
  Scenario: Drop nothing
    Given player content:
      | title | field_game | field_location | field_inventory                                              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 38    | pinecone,pinecone,pinecone,pinecone,pinecone,pinecone,garnet | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 38    | diamond,pinecone                                             | 1            | Flo                | Flo                   |
    When Joe performs "drop"
    Then Joe should see "...Oh, really? I never knew that!\n"
    And Flo should see "Joe is looking a little queer!"


  @pinecone @fountain
  Scenario: Drop pinecone in fountain
    Given player content:
      | title | field_game | field_location | field_inventory                                              | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 38    | pinecone,pinecone,pinecone,pinecone,pinecone,pinecone,garnet | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 38    | diamond,pinecone                                             | 1            | Flo                | Flo                   |
      | Mo    | kyrandia   | Location 38    | diamond                                                      | 1            | Mo                 | Mo                    |
    And the current pinecone count is set to 0

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_blessed |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   | 1                      |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_blessed |
      | kyrandia_profile_Flo | Flo          | 1                        | 2                    | 11                  | 0                      |
    And "location 38" has no scroll
    And the Kyrandia random number will generate 38

    When Joe performs "drop garnet in fountain"
    Then Joe should see "...The fountain sparkles magically in acceptance of your gift!\n"
    And Flo should see "***\nJoe is has just tossed something into the fountain, which sparkles\nmagically in acceptance of the gift!\n"
    And the current pinecone count should be 0

    And Flo should not be blessed
    And Joe should be blessed

    When Joe performs "drop pinecone in fountain"
    Then Joe should see
    """
    ...As you toss the pinecone into the fountain, a voice echoes all around,
    whispering in the wind: "The fountain needs more to work its magic!"

    """
    And Flo should see
    """
    ***
As Joe tosses the pinecone into the fountain, a voice echoes all around,
whispering in the wind: "The fountain needs more to work its magic!"

    """
    And the current pinecone count should be 1


    When Joe performs "drop pinecone in fountain"
    Then Joe should see
    """
    ...As you toss the pinecone into the fountain, a voice echoes all around,
    whispering in the wind: "The fountain needs more to work its magic!"

    """
    And Flo should see
    """
    ***
As Joe tosses the pinecone into the fountain, a voice echoes all around,
whispering in the wind: "The fountain needs more to work its magic!"

    """
    And the current pinecone count should be 2

    When Joe performs "drop pinecone in fountain"
    Then Joe should see
    """
    ...As you toss the pinecone into the fountain, a genie suddenly appears
    above the fountain and states: "Thanks for you donation; a scroll has been
    delivered somewhere within the forest of Kyrandia as a sign of our thanks."
    ***
    The genie then vanishes!

    """
    And Flo should see
    """
    ***
    As Joe tosses a pinecone into the fountain, a genie suddenly appears
    above the fountain and states: "Thanks for you donation; a scroll has been
    delivered somewhere within the forest of Kyrandia as a sign of our thanks."
    ***
    The genie then vanishes!

    """
    And the current pinecone count should be 0
    And scroll item should be in "Location 38"

    When Flo performs "drop pinecone in fountain"
    Then Flo should see
    """
    ...As you toss the pinecone into the fountain, a voice echoes all around,
    whispering in the wind: "The fountain needs more to work its magic!"

    """
    And Joe should see
    """
    ***
As Flo tosses the pinecone into the fountain, a voice echoes all around,
whispering in the wind: "The fountain needs more to work its magic!"

    """
    And the current pinecone count should be 0

    # Doesn't have a pinecone.
    When Mo performs "drop pinecone in fountain"
    Then Joe should see "Mo is looking a little queer!"
    And Mo should see "...Oh, really? I never knew that!\n"

  @shard @fountain
  Scenario: Drop pinecone in fountain
    Given player content:
      | title | field_game | field_location | field_inventory                            | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 38    | shard,shard,shard,shard,shard,shard,garnet | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 38    | diamond,shard                              | 1            | Flo                | Flo                   |
      | Mo    | kyrandia   | Location 38    | diamond                                    | 1            | Mo                 | Mo                    |
    And the current shard count is set to 0

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_blessed |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   | 1                      |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_blessed |
      | kyrandia_profile_Flo | Flo          | 1                        | 2                    | 11                  | 0                      |
    And Joe should not have amulet in inventory

    When Joe performs "drop shard in fountain"
    Then Joe should see
    """
    ...As you toss the shard into the fountain, a voice echoes all around,
    whispering in the wind: "The fountain needs more to work its magic!"

    """
    And Flo should see
    """
    ***
    Joe is has just tossed something into the fountain, which sparkles
    magically in acceptance of the gift!

    """
    And the current shard count should be 1

    When Joe performs "drop shard in fountain"
    Then Joe should see
    """
    ...As you toss the shard into the fountain, a voice echoes all around,
    whispering in the wind: "The fountain needs more to work its magic!"

    """
    And Flo should see
    """
    ***
    Joe is has just tossed something into the fountain, which sparkles
    magically in acceptance of the gift!

    """
    And the current shard count should be 2

    When Joe performs "drop shard in fountain"
    Then Joe should see
    """
    ...As you toss the shard into the fountain, a voice echoes all around,
    whispering in the wind: "The fountain needs more to work its magic!"

    """
    And Flo should see
    """
    ***
    Joe is has just tossed something into the fountain, which sparkles
    magically in acceptance of the gift!

    """
    And the current shard count should be 3

    When Joe performs "drop shard in fountain"
    Then Joe should see
    """
    ...As you toss the shard into the fountain, a voice echoes all around,
    whispering in the wind: "The fountain needs more to work its magic!"

    """
    And Flo should see
    """
    ***
    Joe is has just tossed something into the fountain, which sparkles
    magically in acceptance of the gift!

    """
    And the current shard count should be 4

    When Joe performs "drop shard in fountain"
    Then Joe should see
    """
    ...As you toss the shard into the fountain, a voice echoes all around,
    whispering in the wind: "The fountain needs more to work its magic!"

    """
    And Flo should see
    """
    ***
    Joe is has just tossed something into the fountain, which sparkles
    magically in acceptance of the gift!

    """
    And the current shard count should be 5

    When Joe performs "drop shard in fountain"
    Then Joe should see
    """
    ...As you toss the shard into the fountain, a genie magically appears
    for a moment, hands you an amulet, and then vanishes!

    """
    And Flo should see
    """
    ***
    Joe is has just tossed something into the fountain, which sparkles
    magically in acceptance of the gift!

    """
    And the current shard count should be 0
    And Joe should have amulet in inventory
    And Joe should not have shard in inventory


  @sword @dagger @reflectingpool
  Scenario: Drop pinecone in fountain
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 182   | dagger          | 1            | Joe                | Joe                   |
      | Flo   | kyrandia   | Location 182   | diamond         | 1            | Flo                | Flo                   |

    And the "kyrandia_profile" "kyrandia_profile_Joe" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_blessed |
      | kyrandia_profile_Joe | Joe          | 0                        | 2                    | 1                   | 1                      |
    And the "kyrandia_profile" "kyrandia_profile_Flo" content is deleted
    And kyrandia_profile content:
      | title                | field_player | field_kyrandia_is_female | field_kyrandia_level | field_kyrandia_gold | field_kyrandia_blessed |
      | kyrandia_profile_Flo | Flo          | 1                        | 2                    | 11                  | 0                      |
    And Joe should not have sword in inventory

    When Joe performs "drop dagger in pool"
    Then Joe should see
    """
    ...As you toss the dagger into the pool, it vanishes in circles of ripples.
    ***
    Suddenly, a beautiful sword rises from the water and levitates into your
    hands!

    """
    And Flo should see
    """
    ***
    Joe is playing around in the pool.

    """
    And Joe should have sword in inventory
    And Joe should not have dagger in inventory

    # Doesn't have a dagger now.
    When Joe performs "drop dagger in pool"
    Then Joe should see
    """
    ...Oh, surely thou jest!

    """
    And Flo should see
    """
    ***
    Joe is playing around in the pool.

    """
