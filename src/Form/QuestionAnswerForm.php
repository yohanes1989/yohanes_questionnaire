<?php

namespace Drupal\yohanes_questionnaire\Form;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\yohanes_questionnaire\Entity\QuestionnaireSubmission;

/**
 * Class QuestionAnswerForm.
 *
 * @package Drupal\yohanes_questionnaire\Form
 */
class QuestionAnswerForm extends FormBase {
    /**
     * @var ContentEntityInterface $entity
     */
    public $entity;
    public $questions;
    private static $count = 0;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
      self::$count += 1;

    return 'question_answer_form_'.self::$count;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $extras = null) {
      if(!\Drupal::currentUser()->hasPermission('take questionnaire')){
          return [
              'unathorized' => [
                  '#markup' => t('Please login to take this Questionnaire.')
              ]
          ];
      }

      if(!$extras['entity'] || !$extras['entity'] instanceof ContentEntityInterface){
          throw new \Exception('Question Answer form requires Entity.');
      }

      $this->entity = $extras['entity'];
      $this->questions = $extras['questions'];

      if(count($this->questions) > 0){
          foreach($this->questions as $delta => $question)
          {
              $options = [];
              foreach($question->get('field_answers') as $answerDelta => $answer){
                  $options[$answerDelta+1] = $answer->get('label')->getValue();
              }

              $fieldType = count($question->getCorrectAnswers()) > 1?'checkboxes':'radios';

              $form['question_'.$delta] = [
                  '#type' => $fieldType,
                  '#title' => $question->label(),
                  '#options' => $options,
                  '#required' => TRUE
              ];
          }

          $form['submit'] = [
              '#type' => 'submit',
              '#value' => $this->t('Submit'),
          ];

          return $form;
      }
  }

  /**
    * {@inheritdoc}
    */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
      $owner = \Drupal::currentUser();

      $answers = [];
      foreach($this->questions as $delta => $question){
          $selectedAnswers = $form_state->getValue('question_'.$delta);

          if(count($selectedAnswers) == 1){
              $selectedAnswers = [$selectedAnswers];
          }

          $answers[] = $selectedAnswers;
      }

      $questionnaireSubmission = QuestionnaireSubmission::create();
      $questionnaireSubmission->setName($this->entity->label().' by '.$owner->getAccountName());
      $questionnaireSubmission->setOwnerId($owner->id());
      $questionnaireSubmission->setQuestionnaire($this->entity);
      $questionnaireSubmission->setQuestions($this->questions);
      $questionnaireSubmission->setAnswers($answers);
      $questionnaireSubmission->save();

      drupal_set_message(t('Your Questionnaire result is :result. Thank you for finishing @questionnaire!', [':result' => $questionnaireSubmission->getResult() + 0, '@questionnaire' => $this->entity->label()]));
  }

}
