<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Generic command plugin implementation.
 *
 * This plugin is used to handle miscellaneous commands that aren't handled by
 * any of the other handlers. It's usually small, useless flavor actions like
 * hug or laugh.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_generic_command_handler",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class GenericCommandHandler extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    // Players say a command at the temple to get to level 3.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;

    $commands = [
      "blink" => ["Blink!", "blinking %s eyes in disbelief!", 0],
      "blush" => ["Blush.", "blushing and turning bright red!", 0],
      "boo" => ["BOO!", "booing and yelling for the hook!", 1],
      "bow" => ["Bow.", "bowing rather modestly.", 0],
      "burp" => ["Urrrrp!", "belching rudely!", 1],
      "cackle" => ["Cackle, cackle!", "cackling frighteningly!", 1],
      "cheer" => ["Rah, rah, rah!", "cheering enthusiastically!", 1],
      "chuckle" => ["Heh, heh, heh.", "chuckling under %s breath.", 1],
      "clap" => ["Clap, clap.", "clapping in admiration.", 0],
      "cough" => ["Ahem.", "coughing loud and harshly.", 1],
      "cry" => ["Awwwww.", "crying %s little heart out.", 1],
      "dance" => ["How graceful!", "dancing with soaring spirits!", 0],
      "fart" => ["Yuck.", "emanating a horrible odor.", 0],
      "frown" => ["Frown.", "frowning unhappily.", 0],
      "gasp" => ["WOW!", "gasping in total amazement!", 1],
      "giggle" => ["Giggle, giggle!", "giggling like a hyena.", 1],
      "grin" => ["What a grin!", "grinning from ear to ear.", 0],
      "groan" => ["Groan!", "groaning with disgust.", 1],
      "growl" => ["Growl!", "growling like a rabid bear!", 1],
      "hiss" => ["Hisss!", "hissing like an angry snake!", 1],
      "howl" => ["Howl!", "howling like a dog in heat!", 1],
      "laugh" => ["What's so funny?", "laughing %s head off!", 1],
      "lie" => ["Comfortable?", "lying down comfortably.", 1],
      "moan" => ["Moan!", "moaning loudly.", 1],
      "nod" => ["Nod.", "nodding in agreement.", 0],
      "piss" => ["If you say so.", "lifting %s leg strangely.", 0],
      "pout" => ["Wasdamatta?", "pouting with tearful eyes.", 1],
      "shit" => ["Find a toilet!", "grunting on %s knees.", 0],
      "shrug" => ["Shrug.", "shrugging with indifference.", 0],
      "sigh" => ["Sigh.", "sighing wistfully.", 1],
      "sing" => ["Lalalala.", "singing a cheerful melody.", 1],
      "sit" => ["Ok, now what?", "sitting down for a bit.", 0],
      "smile" => ["Smile!", "smiling kindly.", 0],
      "smirk" => ["Smirk.", "smirking in disdain.", 0],
      "sneeze" => ["Waaacho!", "sneezing %s brains out!", 0],
      "snicker" => ["Snicker, snicker.", "snickering evily.", 1],
      "sniff" => ["Sniff.", "sniffling woefully.", 0],
      "sob" => ["Sob!", "sobbing pitifully.", 1],
      "whistle" => ["Whistle.", "whistling a faintly familiar tune.", 1],
      "yawn" => ["Aaarhh.", "yawning with boredom.", 1],
    ];

    // Array is keyed by command.
    // [0] is what the acting player sees.
    // [1] is what other players in the location see.
    // [2] is whether this is a speaking command.
    $words = explode(' ', $commandText);
    // Assume the first word is the action.
    $action = $words[0];
    if (array_key_exists($action, $commands)) {
      // Found the action. Do it.
      $commandResult = $commands[$action];
      $result = $commandResult[0];
    }
    return $result;
  }

}
