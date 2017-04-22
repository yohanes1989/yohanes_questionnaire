<?php

namespace Drupal\yohanes_questionnaire\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'question_answer_widget' widget.
 *
 * @FieldWidget(
 *   id = "question_answer_widget",
 *   label = @Translation("Answer field widget"),
 *   field_types = {
 *     "question_answer_field"
 *   }
 * )
 */
class QuestionAnswerWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'size' => 60,
      'placeholder' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    $elements['size'] = [
      '#type' => 'number',
      '#title' => t('Size of textfield'),
      '#default_value' => $this->getSetting('size'),
      '#required' => TRUE,
      '#min' => 1,
    ];
    $elements['placeholder'] = [
      '#type' => 'textfield',
      '#title' => t('Placeholder'),
      '#default_value' => $this->getSetting('placeholder'),
      '#description' => t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = t('Textfield size: @size', ['@size' => $this->getSetting('size')]);
    if (!empty($this->getSetting('placeholder'))) {
      $summary[] = t('Placeholder: @placeholder', ['@placeholder' => $this->getSetting('placeholder')]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['label'] = $element + [
        '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->label) ? $items[$delta]->label : NULL,
      '#size' => $this->getSetting('size'),
      '#placeholder' => $this->getSetting('placeholder'),
      '#maxlength' => $this->getFieldSetting('max_length'),
        '#weight' => 0
    ];

    $element['is_correct'] = [
        '#title' => t('Is Correct answer'),
        '#type' => 'checkbox',
        '#return_value' => 1,
        '#default_value' => isset($items[$delta]->is_correct) ? $items[$delta]->is_correct : NULL,
        '#weight' => 2
    ];

    if($this->fieldDefinition->getFieldStorageDefinition()->getCardinality() == 1){
        $element += [
            '#type' => 'fieldset'
        ];
    }

    return $element;
  }

}
