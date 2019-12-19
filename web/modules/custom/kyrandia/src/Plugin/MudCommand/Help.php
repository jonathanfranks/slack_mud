<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Help command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_help",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Help extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {
    $words = explode(' ', $commandText);
    if (count($words) == 1) {
      $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('HLPMSG');
    }
    else {
      $topic = $words[1];
      switch ($topic) {
        case 'commands':
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('HLPCOM');
          break;

        case 'fantasy':
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('HLPFAN');
          break;

        case 'gold':
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('HLPGOL');
          break;

        case 'hits':
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('HLPHIT');
          break;

        case 'levels':
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('HLPLEV');
          break;

        case 'spells':
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('HLPSPE');
          break;

        case 'winning':
          $results[$actingPlayer->id()][] = $this->gameHandler->getMessage('HLPWIN');
          break;

        default:
          $results[$actingPlayer->id()][] = sprintf($this->gameHandler->getMessage('NOHELP'), $topic);
          break;
      }
    }

    if (!$results) {
      $results[$actingPlayer->id()][] = 'Nothing happens.';
    }
  }

}
