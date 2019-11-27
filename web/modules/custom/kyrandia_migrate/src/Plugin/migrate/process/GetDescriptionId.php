<?php

namespace Drupal\kyrandia_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Smalot\PdfParser\Parser;

/**
 * Extracts location descriptions from MSG file.
 *
 * @MigrateProcessPlugin(
 *   id = "kyrandia_get_description_id"
 * )
 *
 * @code
 *   pdf_text:
 *     plugin: kyrandia_get_description_id
 *     source: filename
 * @endcode
 */
class GetDescriptionId extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $ids = [];
    $id_contents = file_get_contents($this->configuration['index_file_path']);
    $raw_ids = explode("\n", $id_contents);
    foreach ($raw_ids as $raw_id) {
      $raw_id = trim($raw_id);
      if ($raw_id) {
        $pos_begin = strpos($raw_id, '{');
        $pos_end = strpos($raw_id, '}');
        $id_string = substr($raw_id, $pos_begin + 1, $pos_end - $pos_begin);
        $pos_comma = strpos($id_string, ',');
        $id = substr($id_string, 0, $pos_comma);
        $ids[] = $id;
      }
    }

    $id_value = $ids[$value];

    return $id_value;
  }

}
