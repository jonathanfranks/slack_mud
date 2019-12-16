<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

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
    $result = [];
    $profile = $this->gameHandler->getKyrandiaProfile($actingPlayer);
    if ($profile) {
      if (count($profile->field_kyrandia_spellbook) == 0) {
        $result[$actingPlayer->id()][] = t('You currently have no spells in your spellbook.');
      }
      else {
        $spells = [];
        foreach ($profile->field_kyrandia_spellbook as $spell) {
          $spells[] = $spell->entity->getName();
        }
        $spellList = $this->wordGrammar->getWordList($spells);
        $result[$actingPlayer->id()][] = t('Your spellbook currently contains :spells.', [':spells' => $spellList]);
      }
    }
    return $result;
  }

}
