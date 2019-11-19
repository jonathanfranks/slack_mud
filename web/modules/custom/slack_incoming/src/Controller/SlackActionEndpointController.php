<?php

namespace Drupal\slack_incoming\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ThedCoreHomeController.
 *
 * @package Drupal\thed_core\Controller
 */
class SlackActionEndpointController extends ControllerBase {

  /**
   * Empty return.
   *
   * @return mixed
   *   Response.
   */
  public function action(Request $request) {
    $rawContent = $request->getContent();
    $package = json_decode($rawContent, TRUE);
    if (array_key_exists('type', $package)) {
      switch ($package['type']) {
        case 'url_verification':
          return new JsonResponse(['challenge' => $package['challenge']]);

        case 'event_callback':
          $event = $package['event'];
          if ($event['type'] == 'message') {

            $config = $this->config('slack.settings');
            $username = $config->get('slack_username');
            $channel = $event['channel'];
            $message = 'Got your message: ' . $event['text'];
            $response = \Drupal::service('slack.slack_service')->sendMessage($message, $channel, $username);

            return new Response('', 200);
          }
          break;
      }
    }
  }

}
