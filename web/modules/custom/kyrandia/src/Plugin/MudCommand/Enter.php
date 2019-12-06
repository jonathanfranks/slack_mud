<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Enter command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_enter",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Enter extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    if ($commandText == 'enter portal' && $loc->getTitle() == 'Location 184') {
      $results = [];
      // Entering the portal gives a standard message...
      $results[] = "As you walk into the portal, you are suddenly blinded by spectacular and colorful images beyond anything you have ever seen before!  As your eyes start to adjust to the brilliance, a magical scene appears before you...\n***";
      // Then a random message from one of these.
      $portalMessages = [
        "You're standing near a beautiful waterfall in the center of an underground forest. Memories of young loves appear before you, so realistic you feel as if you could reach out and touch them. Your heart begins to race faster and faster...",
        "You're lying in a small, wooden chapel. Next to you, you feel the warm touch of your life's eternal love, holding you close. All you can remember is the dark wood of the rafters above as you journey to paradise...",
        "You're crying on the floor, begging for the return of the love you lost long ago. Tears fall like raindrops from your red, swollen eyes as your heart breaks from the sorrow of losing the meaning and inspiration of your life...",
        "You're standing alone in solitude on a dark, windy beach. You look across the black horizon remembering times of happiness and peace long ago, as the world moves on emotionlessly...",
        "You're standing by a statue which you dimly recognize in your subconscious, but your mind is filled with thoughts of determination, inspiration, and above all else, hope. You feel a power growing within your soul, as a smile returns to your face, and a sparkle lights in your eyes...",
        "You're flying through the air on the wings of a pegasus, soaring high above the forest below. It is the most exhilarating feeling of freedom you could have ever imagined...",
        "You're riding a magical carnival ride, holding the hand of your dearest love. As you soar through the evening sky, you experience the joy of heaven...",
        "A single name echos through your mind: Kyra.",
        "Voices vibrate through your head, whispering the words: \"Across time and space, I love you forever...\"",
      ];
      $randomResult = array_rand($portalMessages);
      $results[] = $portalMessages[$randomResult];
      // Then an ending message.
      $results[] = "***\nSuddenly you are thrown back into the chamber as the vision vanishes before your eyes!";
      $result = implode("\n", $results);
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
