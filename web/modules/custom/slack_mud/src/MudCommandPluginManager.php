<?php

namespace Drupal\slack_mud;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Manages discovery and instantiation of MUD Command plugins.
 *
 * @todo Add documentation to this class.
 *
 * @package Drupal\slack_mud
 *
 * @see \Drupal\slack_mud\MudCommandPluginInterface
 */
class MudCommandPluginManager extends DefaultPluginManager {

  /**
   * Constructs the MudCommandPluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/MudCommand', $namespaces, $module_handler, 'Drupal\slack_mud\MudCommandPluginInterface', 'Drupal\slack_mud\Annotation\MudCommandPlugin');
    $this->alterInfo('slack_mud_command_info');
    $this->setCacheBackend($cache_backend, 'slack_mud_command_plugins');
  }

}
