<?php

namespace Drupal\kyrandia;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Manages discovery and instantiation of Kyrandia Command plugins.
 *
 * @todo Add documentation to this class.
 *
 * @package Drupal\kyrandia
 *
 * @see \Drupal\kyrandia\KyrandiaCommandPluginInterface
 */
class KyrandiaCommandPluginManager extends DefaultPluginManager {

  /**
   * Constructs the KyrandiaCommandPluginManager object.
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
    parent::__construct('Plugin/KyrandiaCommand', $namespaces, $module_handler, 'Drupal\kyrandia\KyrandiaCommandPluginInterface', 'Drupal\kyrandia\Annotation\KyrandiaCommandPlugin');
    $this->alterInfo('kyrandia_command_info');
    $this->setCacheBackend($cache_backend, 'kyrandia_command_plugins');
  }

}
