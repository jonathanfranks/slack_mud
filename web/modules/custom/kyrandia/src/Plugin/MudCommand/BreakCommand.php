<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Break command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_break",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class BreakCommand extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 204') {
      $this->breakWand($commandText, $actingPlayer, $result);
    }
    if (!$result) {
      $result[$actingPlayer->id()][] = 'Nothing happens.';
    }
    return $result;
  }

  /**
   * Handles breaking the wand.
   *
   * @param string $commandText
   *   The command text.
   * @param \Drupal\node\NodeInterface $actingPlayer
   *   The player.
   * @param array $result
   *   The result array.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function breakWand(string $commandText, NodeInterface $actingPlayer, array &$result) {
    $words = explode(' ', $commandText);
    $loc = $actingPlayer->field_location->entity;
    // Word 0 has to be 'aim', otherwise we wouldn't be here.
    // We're looking for 'aim wand at tree'.
    $itemPos = array_search('wand', $words);
    if ($itemPos !== FALSE) {
      if ($this->gameHandler->takeItemFromPlayer($actingPlayer, 'wand')) {
        if ($this->gameHandler->playerHasItem($actingPlayer, 'kyragem')) {
          $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('RABOM0');
          $othersMessage = sprintf($this->gameHandler->getMessage('RABOM1'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
        }
        else {
          $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('RABOM2');
          $othersMessage = sprintf($this->gameHandler->getMessage('RABOM3'), $actingPlayer->field_display_name->value);
          $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $result);
          $this->gameHandler->giveItemToPlayer($actingPlayer, 'kyragem');
        }
      }
    }
    if (!$result) {
      $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('WALM01');
    }
  }

}
