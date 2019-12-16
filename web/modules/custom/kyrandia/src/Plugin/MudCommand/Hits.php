<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Hits command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_hits",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Hits extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    if ($profile) {
      $result = t('You have :gold gold piece:plural.', [
        ':gold' => $profile->field_kyrandia_gold->value,
        ':plural' => $profile->field_kyrandia_gold->value == 1 ? '' : 's',
      ]);
    }
    return $result;
  }

}
