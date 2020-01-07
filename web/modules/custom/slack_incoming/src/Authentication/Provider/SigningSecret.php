<?php

namespace Drupal\slack_incoming\Authentication\Provider;

use Drupal\Core\Access\AccessCheckInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * Slack incoming message signing secret authentication provider.
 */
class SigningSecret implements AccessCheckInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * SigningSecret constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->config = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public function applies(Route $route) {
    $requirements = $route->getRequirements();
    $applicable_requirements = [
      '_slack_incoming_signing_secret',
    ];
    $requirement_keys = array_keys($requirements);

    if (array_intersect($applicable_requirements, $requirement_keys)) {
      // No method requirement given, so we run this access check to be on the
      // safe side.
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function access(Request $request, AccountInterface $account) {
    // We need headers HTTP_X_SLACK_SIGNATURE and HTTP_X_SLACK_REQUEST_TIMESTAMP
    // and a valid timestamp.
    if (empty($_SERVER['HTTP_X_SLACK_SIGNATURE']) || empty($_SERVER['HTTP_X_SLACK_REQUEST_TIMESTAMP'])) {
      return AccessResult::forbidden();
    }

    // We aren't checking any accounts. Anon is okay.
    $rawContent = $request->getContent();
    $config = $this->config->get('slack_incoming.slackapplicationconfig');
    $signingSecret = $config->get('signing_secret');
    $version = explode("=", $_SERVER['HTTP_X_SLACK_SIGNATURE']);
    $timestamp = $_SERVER['HTTP_X_SLACK_REQUEST_TIMESTAMP'];
    if (abs(time() - $timestamp) > 60 * 5) {
      // Repeat request? More than 5 minutes old. Ignore it.
      return AccessResult::forbidden();
    }
    $sigBaseString = "{$version[0]}:$timestamp:$rawContent";
    $hashSignature = hash_hmac('sha256', $sigBaseString, $signingSecret);
    if (!hash_equals($_SERVER['HTTP_X_SLACK_SIGNATURE'], "v0=$hashSignature")) {
      return AccessResult::forbidden();
    }
    return AccessResult::allowed();
  }

}
