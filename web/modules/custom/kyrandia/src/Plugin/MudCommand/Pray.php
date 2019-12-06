<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Pray command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_pray",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Pray extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Offers hints at silver altar and temple, otherwise just a nothing action.
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->getKyrandiaProfile($actingPlayer);
    if ($loc->getTitle() == 'Location 24') {
      // Player is at the silver altar.
      $result = "As you pray, a vision of the Goddess Tashanna appears before you, shining in her eternal, radiant beauty. She smiles at you and says: \"Oh, brave and courageous one, there is so much you must learn. What you have seen is only a small fraction of the world of Kyrandia... don't let your pride outmatch your knowledge. Search for the truth of the four elements of all life, and know your corresponding birthstones, and their relation to the forces of nature and magic. I bid thee the best of luck.\"  The goddess then vanishes as mysteriously as she had appeared.";
    }
    elseif ($loc->getTitle() == 'Location 7') {
      // In the temple.
      $result = "As you pray, a vision of the Goddess Tashanna appears in your mind, standing before you in a holy brilliance of light. She smiles and speaks softly to you: \"One of your many quests must be the realization of your astral origins; seek thy birthstones and prove thy knowledge.";
    }
    elseif ($loc->getTitle() == 'Location 27') {
      // At the rock. Player only has to pray once for the mists to come.
      $result = "Your prayers are heard.\n***\nThe mists around the rock begin to swirl magically!\n";
      $game = $actingPlayer->field_game->entity;
      $this->saveInstanceSetting($game, 'currentRockPrayCount', 1);
    }
    if (!$result) {
      $result = 'As you pray, a vision of the Goddess Tashanna appears in your mind, she smiles at you, and offers her blessings.';
    }
    return $result;
  }

}
