<?php

namespace Drupal\yohanes_questionnaire\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'question_entity_reference_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "question_entity_reference_formatter",
 *   label = @Translation("Questions Form"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class QuestionEntityReferenceFormatter extends EntityReferenceFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Implement default settings.
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
      // Implement settings form.
    ] + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
      $elements = [];
      $questions = [];

      foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
          $questions[$delta] = $entity;

          $label = $entity->label();

          $elements[$delta] = [
              '#plain_text' => $label
          ];
          $elements[$delta]['#cache']['tags'] = $entity->getCacheTags();
      }

      $form = \Drupal::formBuilder()->getForm('Drupal\yohanes_questionnaire\Form\QuestionAnswerForm', [
          'questions' => $questions,
          'entity' => $items->getEntity()
      ]);

      return $form;
      //return $elements;
  }
}
