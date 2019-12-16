<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Devote command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_devote",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Devote extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = [];
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 295') {
      $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
      if ($profile->field_kyrandia_level->entity->getName() == '16') {
        // Player needs broach, pendant, locket, and ring.
        $hasBroach = $this->gameHandler->playerHasItem($actingPlayer, 'broach');
        $hasPendant = $this->gameHandler->playerHasItem($actingPlayer, 'pendant');
        $hasLocket = $this->gameHandler->playerHasItem($actingPlayer, 'locket');
        $hasRing = $this->gameHandler->playerHasItem($actingPlayer, 'ring');
        if ($hasBroach && $hasPendant && $hasLocket && $hasRing) {
          $this->gameHandler->advanceLevel($profile, 17);
          $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('DEVM01');
          $othersMessage = sprintf($this->gameHandler->getMessage('DEVM02'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
          // Remove broach, pendant, locket, and ring.
          $this->gameHandler->takeItemFromPlayer($actingPlayer, 'broach');
          $this->gameHandler->takeItemFromPlayer($actingPlayer, 'pendant');
          $this->gameHandler->takeItemFromPlayer($actingPlayer, 'locket');
          $this->gameHandler->takeItemFromPlayer($actingPlayer, 'ring');
        }
        else {
          $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('DEVM03');
          $othersMessage = sprintf($this->gameHandler->getMessage('DEVM04'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
        }
      }
    }
    if (!$result) {
      $result[$actingPlayer->id()][] = 'Nothing happens.';
    }
    return $result;
  }

}
