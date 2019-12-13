<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Kyrandia-specific Look command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_look",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Look extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    // Players drop things into the stump at Loc 18 to reach level 6.
    $result = [];
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->getKyrandiaProfile($actingPlayer);
    // The 'to' gets stripped out.
    if (strpos($commandText, 'statue') !== FALSE && $loc->getTitle() == 'Location 181') {
      $result[$actingPlayer->id()][] = $this->statue();
    }
    elseif (strpos($commandText, 'pool') !== FALSE && $loc->getTitle() == 'Location 182') {
      $result[$actingPlayer->id()][] = $this->reflectingPool();
    }
    elseif (strpos($commandText, 'symbols') !== FALSE && $loc->getTitle() == 'Location 183') {
      $result[$actingPlayer->id()][] = $this->pantheonSymbols();
    }
    elseif (strpos($commandText, 'pillars') !== FALSE && $loc->getTitle() == 'Location 183') {
      $result[$actingPlayer->id()][] = $this->pantheonSymbols();
    }
    else {
      // Not a special look. Handle this like a regular look.
      /** @var \Drupal\slack_mud\MudCommandPluginManager $pluginManager */
      $pluginManager = \Drupal::service('plugin.manager.mud_command');
      /** @var \Drupal\slack_mud\MudCommandPluginInterface $plugin */
      $plugin = $pluginManager->createInstance('look');
      $result = $plugin->perform($commandText, $actingPlayer);
    }
    if (!$result) {
      $result[$actingPlayer->id()][] = 'Nothing happens.';
    }
    return $result;
  }

  /**
   * Handles looking at the statue.
   *
   * @return string
   *   The result.
   */
  protected function statue() {
    return "The statue is quite a spectacular work of art, especially for its large proportions.  Despite the wonderful artwork, your attention is grasped by an inscription near the base which reads: \"id- upi- ozs-omsy-pm -p p-ysom y-r fs--rt\".";
  }

  /**
   * Handles looking at the statue.
   *
   * @return string
   *   The result.
   */
  protected function reflectingPool() {
    return "As you look into the pool, you see a mirror image of yourself.\n
***\n
Suddenly, the words \"feoamut wotaquop jagarooni pistobba\" appear in the pool, and then mysteriously vanish!";
  }

  /**
   * Handles looking at the symbols.
   *
   * @return string
   *   The result.
   */
  protected function pantheonSymbols() {
    return "As you examine the ancient runes embedded into the marble pillars, you are only able to recognize a few of the symbols, although you cannot instantly translate their true meaning. You read: \"ha-tc en-esa-eop ore-ef gusi- xx-he xxx-e-\".";
  }

}
