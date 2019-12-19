@api @help
Feature: Help commands

  @commands
  Scenario: Help commands
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 184   | wand            | 1            | Joe                | Joe                   |
    When Joe performs "help commands"
    Then Joe should see
      """
      ...Suddenly, a small elf runs out from nowhere!  He gives you a nod and a
      smile and whispers in your ear...

       Kyrandia is a multi-player fantasy adventure.  You can speak with other
       players using action verbs.  For example, if you were to type "say hello
       there", other players at the same location would see "hello there."

       You can move around the large world of Kyrandia by telling the computer
       which direction you wish to go (example: west).  You can abbreviate these
       commands for your convenience: n(orth), s(outh), e(ast), and w(est).  In
       some instances, you can also use special commands too (example: climb the
       tree).  Also, it is rumored travel can be done magically as well!

       You can pick up objects and use them in assorted ways (you can experiment
       for yourself).  Also, for added novelty, you can do numerous "mundane"
       actions, such as: smile, laugh, kick <Player-ID>, wink, hug <Player-ID>,
       and many more!

       The rest you have to figure out on your own!  Good luck!

      ***
      The elf then bows swiftly to you and disappears in a puff of smoke!

      """


  @fantasy
  Scenario: Help fantasy
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 184   | wand            | 1            | Joe                | Joe                   |
    When Joe performs "help fantasy"
    Then Joe should see "\n\n It has taken over two years to fully visualize this Fantasy World of\n Kyrandia, a mirror-image of a dreamworld inspired by love and fashioned\n by imagination.  Created with smiles and tears, hopes and fears, and a\n passion of the soul immeasurable by mortal definitions, Kyrandia holds\n a magic beyond sorcery; a magic that will bloom like the lavender roses\n of Kyrandia across time and space forever... love.  This entire work is\n dedicated to my Lady of Legends, the one who dances eternally in my\n dreams; forever alive in my heart and soul, beyond reality, but never\n beyond imagination and hope, over the rainbow, in the fantasy forest where\n wishes come true and the emeralds sparkle in the waterfall forever more.\n\n Although Kyrandia is an adventure game, filled with action, excitement,\n and interaction, I ask you to also take some time amongst the casting of\n fireballs and battling of monsters to examine the scenery of Kyrandia\n and delve into the deeper meanings of this visionary Fantasy World.\n\n Thank you so much for partaking in this humble world of mine.  I wish\n you the best of luck, in Kyrandia, and in everything in life.  Farewell.\n\n        Scott James Brinker\n    (fantasy author/programmer)\n\n"

  @gold
  Scenario: Help gold
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 184   | wand            | 1            | Joe                | Joe                   |
    When Joe performs "help gold"
    Then Joe should see
    """
    ...As you ask for a help, a genie suddenly pops out in a cloud of smoke,
    smiles, and says to you...

     Welcome to Kyrandia!  I'm so pleased and honored to have you in my humble
     world.  Thank you so, so much for joining this wonderful fantasy land!

     However, you wish to know about gold, huh?  Well, as you've probably
     already concluded, gold is the currency of Kyrandia, similar to the
     "dollar" of your original world.  You can use gold to buy things from
     the many different people you will meet in Kyrandia, or for other,
     more arcane purposes.

     To get gold, well, you have to get it the hard way -- find it.  Or, if
     you're a good alchemist, you might be able to make it?  Ah, well that's
     up to you to discover.  If you're having trouble finding gold out in the
     vast wilderness of Kyrandia, you could always try and get a job.  Rumor
     has it many village store keepers are always looking for good adventurers.

     Anyway, hope I've been of some assistance, but I have to run for now.  Have
     a great time, and I bid you well on all your adventures in Kyrandia!

    ***
    The genie disappears in a flash of forked, blue lightning!

    """

  @hits
  Scenario: Help hits
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 184   | wand            | 1            | Joe                | Joe                   |
    When Joe performs "help hits"
    Then Joe should see
    """
    ...As you make your request for some aid, an old, wise man suddenly appears
    before you, gives you a small grin, and says...

     "Hit points" are the representation of your character's strength, health,
     and endurance.  When your hit points reaches zero, your character dies.
     As you grow in power (gain levels) your number of hit points increase.

     You combat with people through the use of magic, ultimately to bring their
     hit points to zero.  You can use spells or magical items, but the result is
     usually the same.  However, if you wish to be really slick, you can also
     attack people in other ways besides lowering their hit points -- I'll leave
     it up to you to discover how...

     You can also combat with the many different creatures of Kyrandia, but you
     best beware: their power is often way beyond your magic!

     When a character dies, he (or she) is automatically restarted at the very
     beginning of the game, near the willow tree, at level 1.

    ***
    The old man then gives you a respectful bow and vanishes in a puff of smoke,
    but not before grinning to himself.

    """

  @levels
  Scenario: Help levels
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 184   | wand            | 1            | Joe                | Joe                   |
    When Joe performs "help levels"
    Then Joe should see
    """
    ...As you call out for help, a flash of incredibly bright light blinds you!
    ***
    Before you stands the Goddess Tashanna, shining in sheer beauty.  She kisses
    your check and says...

     Your power is measured in "levels" in Kyrandia, which determine how many
     "hit points" you have, how many "spell points" you have, and how powerful
     of spells you may cast.  You gain 2 spell points per level, and you gain
     4 hit points per level.  (see "help spells" and "help hits").  Each level
     also has a "title", or name, associated with it.  For example, when you
     start at level 1, you are titled "Apprentice"; to win the game, you must
     become an "Arch-Mage of Legends".  (see "help winning").

     It is up to you to discover exactly how many levels there are, and their
     respective titles.  However, everyone wears a "Patch of Sorcery" which
     declares their title (type "look <so-and-so>").  You may also look at
     yourself to admire your own title too.

     Above all else, remember... some things are more powerful than magic!

    ***
    The Goddess Tashanna then smiles at you, and vanishes in a purple flash!

    """

  @spells
  Scenario: Help spells
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 184   | wand            | 1            | Joe                | Joe                   |
    When Joe performs "help spells"
    Then Joe should see
    """
    ...A tall, ominous wizard dressed in deep blue robes suddenly appears before
    you in a column of orange flame!  He bows to you and begins to speak...

     Kyrandia is a world of magic.  To become powerful in Kyrandia, you therefore
     must master the art of spellweaving.

     The spell system of Kyrandia works like this: every player has a "spellbook"
     in which he keeps the spells he can memorize; no one can steal this
     book, and the player cannot lose it.  Once a spell is in the spellbook, a
     player may memorize that spell (type: memorize <spell name>).  You can have
     up to ten spells "memorized" at the same time.  A player then may cast any
     spell he has memorized (type: cast <spell name>).  Some spells also
     require a direct object (a target), such as a person, place, or thing; the
     direct object should be placed right after the spell name when being cast.
     Also note that some spells require certain components (objects) or other
     special circumstances to be successfully cast.

     You must also be of a certain level to cast different spells; the most
     powerful spells can only be cast by those of high enough level.

    ***
    The wizard then bows and vanishes into thin air!

    """

  @winning
  Scenario: Help winning
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 184   | wand            | 1            | Joe                | Joe                   |
    When Joe performs "help winning"
    Then Joe should see
    """
    ...An image of the author, Scott Brinker, appears in a shimmering circle of
    rainbows!  He smiles and says...

     To "win" Kyrandia, you must master the magic of this world and gain the title
     of "Arch-Mage of Legends".  Upon reaching this high level, you are eligible
     to collect any prize the System Operator of this realm may be offering.  The
     game will notify you of how to collect your prize and glory.  Let it be
     known that only the FIRST player to solve Kyrandia (on this system) may be
     the one to receive the prize.

     However, everyone who takes part in the experience of Kyrandia can win; if
     not a materialistic award of credits, then many hours of enjoyable gaming,
     the thrill of magical Dungeons & Dragons combat, and special insight into a
     dreamworld created from true love -- an enchanting, parallel fantasy of the
     world of reality in which we all live.

     I'm sorry, but I can't give you any "hints" or "solutions", because after
     all, that would take away all the excitement and adventure I've created for
     you!  However, I do suggest you see with your heart as well as your eyes...

    ***
    He then shakes your hand, smiles again, and vanishes in his iridescent circle
    of rainbows.

    """

  @missing
  Scenario: Help without a real topic
    Given player content:
      | title | field_game | field_location | field_inventory | field_active | field_display_name | field_slack_user_name |
      | Joe   | kyrandia   | Location 184   | wand            | 1            | Joe                | Joe                   |
    When Joe performs "help nonsense"
    Then Joe should see
    """
    ...Sorry, there is no help available for nonsense.

    """
