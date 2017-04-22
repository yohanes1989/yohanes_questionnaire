<?php

namespace Drupal\yohanes_questionnaire\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class QuestionnaireSubmissionSettingsForm.
 *
 * @package Drupal\yohanes_questionnaire\Form
 *
 * @ingroup yohanes_questionnaire
 */
class QuestionnaireSubmissionSettingsForm extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'QuestionnaireSubmission_settings';
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Empty implementation of the abstract submit class.
  }

  /**
   * Defines the settings form for Questionnaire submission entities.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   Form definition array.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['QuestionnaireSubmission_settings']['#markup'] = 'Settings form for Questionnaire submission entities. Manage field settings here.';
    return $form;
  }

}
