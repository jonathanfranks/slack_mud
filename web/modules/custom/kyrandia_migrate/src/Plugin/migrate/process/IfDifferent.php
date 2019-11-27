<?php

namespace Drupal\kyrandia_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Smalot\PdfParser\Parser;

/**
 * Processes the field if the compare values are different.
 *
 * @MigrateProcessPlugin(
 *   id = "if_different"
 * )
 *
 * @code
 *   field_deny_description/value:
 *     plugin: if_different
 *     source: '@deny_description'
 *     compare:
 *       - description
 *       - deny description
 * @endcode
 */
class IfDifferent extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $text = '';

    $compares = [];

    foreach ($this->configuration['compare'] as $compare) {
      $compares[$compare] = $row->getSourceProperty($compare);
    }

    if (count(array_unique($compares)) !== 1) {
      // More than one unique value, so they aren't the same.
      $text = $value;
    }

    return $text;
  }

}
