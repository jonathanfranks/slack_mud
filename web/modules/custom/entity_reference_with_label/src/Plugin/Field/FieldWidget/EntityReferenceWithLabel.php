<?php

namespace Drupal\entity_reference_with_label\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsSelectWidget;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldWidget(
 *   id = "entity_reference_select_with_label",
 *   label = @Translation("Select w/Label"),
 *   description = @Translation("A select field with an associated label."),
 *   field_types = {
 *     "entity_reference_with_label"
 *   }
 * )
 */
class EntityReferenceWithLabel extends OptionsSelectWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['target_id'] = [
      '#type' => 'select',
      '#options' => $this->getOptions($items->getEntity()),
      '#default_value' => isset($items[$delta]->target_id) ? $items[$delta]->target_id : NULL,
      // Do not display a 'multiple' select box if there is only one option.
      '#multiple' => $this->multiple && count($this->options) > 1,
    ];

    $element['label'] = [
      '#title' => $this->t('Label'),
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]) ? $items[$delta]->label : '',
      '#min' => 1,
      '#weight' => 10,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $new_values = parent::massageFormValues($values, $form, $form_state);
    // Thought that Drupal handled eliminating the blank ones on its own, but
    // it doesn't, so we're manually cleaning them up here.
    foreach ($new_values as $id => $new_value) {
      if ($new_value['target_id'] == '_none') {
        unset($new_values[$id]);
      }
    }
    return $new_values;
  }

}
