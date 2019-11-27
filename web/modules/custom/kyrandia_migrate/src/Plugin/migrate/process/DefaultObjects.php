<?php

namespace Drupal\kyrandia_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Smalot\PdfParser\Parser;

/**
 * Processes the default objects field.
 *
 * @MigrateProcessPlugin(
 *   id = "default_objects"
 * )
 *
 * @code
 *   default_objects:
 *     plugin: default_objects
 *     source: "Objs' Idx"
 *     number: "# of Objs"
 * @endcode
 */
class DefaultObjects extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $rawItemIds = explode(',', $value);
    $item_ids = array_filter($rawItemIds);
    $keyedItemIds = [];
    foreach ($item_ids as $item_id) {
      $keyedItemIds[] = ['item' => $item_id];
    }
    return $keyedItemIds;
  }

}
