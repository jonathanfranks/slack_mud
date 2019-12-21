<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Kyrandia-specific Say command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_say",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Say extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $loc = $actingPlayer->field_location->entity;
    if ($loc->getTitle() == 'Location 183' && $commandText == 'say legends pass and time goes by but true love will never die') {
      if ($this->gameHandler->giveItemToPlayer($actingPlayer, 'key')) {
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('PANM00');
        $othersMessage = sprintf($this->gameHandler->getMessage('PANM01'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
      else {
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('PANM02');
        $othersMessage = sprintf($this->gameHandler->getMessage('PANM01'), $actingPlayer->field_display_name->value);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
      }
    }
    else {
      $words = explode(' ', $commandText);
      if (count($words) == 1) {
        // Just "say", no words or anything.
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('HUH');
        $this->sndutl($actingPlayer, 'opening %s mouth speechlessly.', $results);
      }
      else {
        $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('SAIDIT');
        // Take first word as the verb.
        $verb = array_shift($words);
        // The sentence is the rest of the command text.
        $sentence = implode(' ', $words);
        // We handle line breaks differently and we break things up as different
        // elements in the array. We'll handle this by constructing the output
        // on one line.
        $othersMessage = sprintf($this->gameHandler->getMessage('SPEAK1'), $actingPlayer->field_display_name->value, $verb) .
        sprintf($this->gameHandler->getMessage('SPEAK2'), $sentence);
        $this->gameHandler->sendMessageToOthersInLocation($actingPlayer, $loc, $othersMessage, $results);
        $nearbyMessage = $this->gameHandler->getMessage('SPEAK3');
        $this->sndnear($actingPlayer, $nearbyMessage, $results);
      }
    }
  }

}
