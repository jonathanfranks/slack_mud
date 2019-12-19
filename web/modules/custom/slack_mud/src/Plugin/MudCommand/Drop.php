<?php

namespace Drupal\slack_mud\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Drop command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "drop",
 *   module = "slack_mud"
 * )
 *
 * @package Drupal\slack_mud\Plugin\MudCommand
 */
class Drop extends MudCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    // Now remove the DROP and we'll see who or what they're taking.
    $target = str_replace('drop', '', $commandText);
    $target = trim($target);
    $article = $this->wordGrammar->getIndefiniteArticle($target);

    $loc = $actingPlayer->field_location->entity;

    $item = $this->gameHandler->playerHasItem($actingPlayer, $target, TRUE);
    if ($item) {
      // Player has the item.
      $this->gameHandler->placeItemInLocation($loc, $item->getTitle());
      $results[$actingPlayer->id()][] = t('You dropped the :item.', [':item' => $item->getTitle()]);
    }
    else {
      $results[$actingPlayer->id()][] = t("You don't have :article :target.", [
        ':article' => $article,
        ':target' => $target,
      ]);
    }
  }

}
