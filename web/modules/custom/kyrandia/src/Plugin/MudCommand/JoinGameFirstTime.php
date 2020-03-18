<?php

namespace Drupal\kyrandia\Plugin\MudCommand;

use Drupal\node\NodeInterface;
use Drupal\slack_mud\MudCommandPluginInterface;

/**
 * Defines JoinGameFirstTime command plugin implementation.
 *
 * This fires when the player joins the game for the first time.
 *
 * @MudCommandPlugin(
 *   id = "kyrandia_joingamefirsttime",
 *   module = "kyrandia"
 * )
 *
 * @package Drupal\kyrnandia\Plugin\MudCommand
 */
class JoinGameFirstTime extends KyrandiaCommandPluginBase implements MudCommandPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function perform($commandText, NodeInterface $actingPlayer, array &$results) {

    $displayName = $actingPlayer->field_display_name->value;

    $message = '[
  {
    "type": "section",
    "block_id": "sectionDisplayNAme",
    "text": {
      "type": "mrkdwn",
      "text": "Your character\'s name is ' . $displayName . ' This is how other players will see you in the world of Kyrandia."
    }
  },
  {
    "type": "section",
    "block_id": "sectionSelectGender",
    "text": {
      "type": "mrkdwn",
      "text": "Select your character\'s gender. (This game is from 1988 and these are the only available choices.)"
    }
  },
		{
			"type": "actions",
			"elements": [
				{
					"type": "button",
					"text": {
						"type": "plain_text",
						"text": "Male",
						"emoji": true
					},
					"value": "setgendermale"
				},
				{
					"type": "button",
					"text": {
						"type": "plain_text",
						"text": "Female",
						"emoji": true
					},
					"value": "setgenderfemale"
				}
			]
		}
]
';

    // We'll return block text if we return an array. Since we wrote the JSON
    // as a string, we need to convert it.
    $results[$actingPlayer->id()][] = json_decode($message, TRUE);
  }

}
