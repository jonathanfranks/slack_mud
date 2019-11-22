<?php

namespace Drupal\entity_reference_with_label\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceLabelFormatter;

/**
 * @FieldFormatter(
 *   id = "entity_reference_with_label_view",
 *   label = @Translation("Entity label and custom label"),
 *   description = @Translation("Display the referenced entities’ label with
 *   their custom labels."),
 *   field_types = {
 *     "entity_reference_with_label"
 *   }
 * )
 */
class EntityReferenceWithLabel extends EntityReferenceLabelFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);
    $values = $items->getValue();

    foreach ($elements as $delta => $entity) {
      $elements[$delta]['#suffix'] = ' (' . $values[$delta]['label'] . ')';
    }

    return $elements;
  }
}
