<?php

namespace Drupal\slack_mud\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SlackMudCommandController.
 */
class SlackMudCommandController extends ControllerBase {

  /**
   * Slack command endpoint to list the games currently available.
   */
  public function games(Request $request) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'game')
      ->sort('title');
    $nids = $query->execute();
    $nodes = Node::loadMultiple($nids);
    $gameTitles = [];
    foreach ($nodes as $node) {
      $gameTitles[] = $node->getTitle();
    }
    $gameTitleText = implode("\n", $gameTitles);
    $return = [
      'blocks' => [
        [
          'type' => 'section',
          'text' => [
            'type' => 'mrkdwn',
            'text' => "We have the following games:\n" . $gameTitleText,
          ],
        ],
      ],
    ];
    return new JsonResponse($return);
  }

  /**
   * Slack command endpoint to join a game.
   */
  public function joinGame(Request $request) {
    $content = $request->getContent();
    parse_str($content, $command);
    if (array_key_exists('text', $command)) {
      $game = $command['text'];

      $query = \Drupal::entityQuery('node')
        ->condition('type', 'game')
        ->condition('title', $game);
      $gameNids = $query->execute();
      if (!$gameNids) {
        // Game doesn't exist. Send an error.
        $return = [
          'blocks' => [
            [
              'type' => 'section',
              'text' => [
                'type' => 'mrkdwn',
                'text' => "We don't have the game $game. Try joining another game. Use the command /games to see the list.",
              ],
            ],
          ],
        ];
      }
      else {
        // Game exists. Join it.
        $gameNid = reset($gameNids);
        $gameNode = Node::load($gameNid);
        $query = \Drupal::entityQuery('node')
          ->condition('type', 'player')
          ->condition('field_slack_user_name', $command['user_id'])
          ->condition('field_game.target_id', $gameNid);
        $playerNids = $query->execute();
        if (!$playerNids) {
          // User doesn't have a player profile for this game.
          // Create one and make it active.
          $userName = $command['user_name'];
          // @TODO Add ability for user to enter a display name.
          $player = Node::create([
            'type' => 'player',
            'title' => $game . '_' . $userName,
            'field_slack_user_name' => $command['user_id'],
            'field_display_name' => $userName,
            'field_location' => $gameNode->field_starting_location->entity->id(),
            'field_game' => $gameNid,
            'field_active' => TRUE,
          ]);
          $player->save();
        }
        else {
          // User already has a player profile for this game.
          // Mark it active.
          $playerNid = reset($playerNids);
          $playerNode = Node::load($playerNid);
          $playerNode->field_active = TRUE;
          $playerNode->save();
        }

        $return = [
          'blocks' => [
            [
              'type' => 'section',
              'text' => [
                'type' => 'mrkdwn',
                'text' => "You have joined $game.",
              ],
            ],
          ],
        ];

      }

    }
    return new JsonResponse($return);
  }

}
