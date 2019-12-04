<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines Spellbook command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_spellbook",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Spellbook extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $profile = $this->getKyrandiaProfile($actingPlayer);
    if ($profile) {
      if (count($profile->field_kyrandia_spellbook) == 0) {
        $result = t('You currently have no spells in your spellbook.');
      }
      else {
        $spells = '';
        $result = t('Your spellbook currently contains :spells', [':spells' => $spells]);
      }
    }
    return $result;
  }

}
