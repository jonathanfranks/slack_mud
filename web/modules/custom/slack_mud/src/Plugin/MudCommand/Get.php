<?php

namespace Drupal\slack_mud\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines Get command plugin implementation.
 *
 * @MudCommandPlugin(
 *   id = "get",
 *   module = "slack_mud"
 * )
 *
 * @package Drupal\slack_mud\Plugin\MudCommand
 */
class Get extends MudCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer) {
    $foundSomething = FALSE;
    $result = '';
    $loc = $actingPlayer->field_location->entity;
    $item = $this->gameHandler->locationHasItem($loc, $commandText, TRUE);
    if ($item) {
      if ($item->field_can_pick_up->value) {
        $this->gameHandler->giveItemToPlayer($actingPlayer, $item->getTitle());
        $result = 'You picked up the ' . $item->getTitle();
        $foundSomething = TRUE;
      }
      else {
        // Can't pick up - show its deny get message.
        $result = $item->field_deny_get_message->value;
      }
    }

    if (!$foundSomething) {
      // Didn't find a matching object, so we'll construct an error sentence.
      $words = explode(' ', $commandText);
      // Assume that word 0 is 'get' and word 1 is the object.
      if (count($words) > 1) {
        $target = $words[1];
        $where = $loc->field_object_location->value;
        $result = t("Sorry, there is no :target :where.",
          [
            ':target' => $target,
            ':where' => $where,
          ]
        );
      }
      else {
        $result = t('What are you talking about?');
      }
    }
    return $result;
  }

}
