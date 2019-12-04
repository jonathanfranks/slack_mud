<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\Event\CommandEvent;
use Drupal\slack_mud\MudCommandPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines Touch command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_touch",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class Touch extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $result = NULL;
    $loc = $actingPlayer->field_location->entity;
    $profile = $this->getKyrandiaProfile($actingPlayer);
    if ($commandText == 'touch orb' && $loc->getTitle() == 'Location 188') {
      // Touch orb in misty ruins (188) teleports to druid's circle (34).
      $query = \Drupal::entityQuery('node')
        ->condition('type', 'location')
        ->condition('field_game.entity.title', 'kyrandia')
        ->condition('title', 'Location 34');
      $ids = $query->execute();
      if ($ids) {
        $id = reset($ids);
        $actingPlayer->field_location = $id;
        $actingPlayer->save();
        $result = "As you touch the orb, you are suddenly pulled through a magical portal...\n";

        // The result is LOOKing at the new location.
        $mudEvent = new CommandEvent($actingPlayer, 'look');
        $mudEvent = $this->eventDispatcher->dispatch(CommandEvent::COMMAND_EVENT, $mudEvent);
        $result .= $mudEvent->getResponse();

      }
    }
    if (!$result) {
      $result = 'Nothing happens.';
    }
    return $result;
  }

}
