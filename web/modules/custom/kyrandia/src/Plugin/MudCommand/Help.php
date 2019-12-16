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
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $words = explode(' ', $commandText);
    if (count($words) == 1) {
      $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('HLPMSG');
    }
    else {
      $topic = $words[1];
      switch ($topic) {
        case 'commands':
          $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('HLPCOM');
          break;

        case 'fantasy':
          $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('HLPFAN');
          break;

        case 'gold':
          $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('HLPGOL');
          break;

        case 'hits':
          $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('HLPHIT');
          break;

        case 'levels':
          $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('HLPLEV');
          break;

        case 'spells':
          $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('HLPSPE');
          break;

        case 'winning':
          $result[$actingPlayer->id()][] = $this->gameHandler->getMessage('HLPWIN');
          break;

        default:
          $result[$actingPlayer->id()][] = sprintf($this->gameHandler->getMessage('NOHELP'), $topic);
          break;
      }
    }

    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
