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
      $result = $this->breakWand($commandText, $actingPlayer);
    }
    if (!$result) {
      $result = 'Nothing happens.';
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
   *
   * @return string
   *   The result.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function breakWand(string $commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $words = explode(' ', $commandText);
    // Word 0 has to be 'aim', otherwise we wouldn't be here.
    // We're looking for 'aim wand at tree'.
    $itemPos = array_search('wand', $words);
    if ($itemPos !== FALSE) {
      $profile = $this->getKyrandiaProfile($actingPlayer);
      if ($this->takeItemFromPlayer($actingPlayer, 'wand')) {
        if ($this->playerHasItem($actingPlayer, 'kyragem')) {
          $result = $this->getMessage('RABOM0');
        }
        else {
          $result = $this->getMessage('RABOM2');
          $this->giveItemToPlayer($actingPlayer, 'kyragem');
        }
      }
    }
    if (!$result) {
      $result = $this->getMessage('WALM01');
    }
    return $result;
  }

}
