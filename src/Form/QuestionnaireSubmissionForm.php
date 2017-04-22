<?php

namespace Drupal\yohanes_questionnaire\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Questionnaire submission edit forms.
 *
 * @ingroup yohanes_questionnaire
 */
class QuestionnaireSubmissionForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\yohanes_questionnaire\Entity\QuestionnaireSubmission */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Questionnaire submission.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Questionnaire submission.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.questionnaire_submission.canonical', ['questionnaire_submission' => $entity->id()]);
  }

}
