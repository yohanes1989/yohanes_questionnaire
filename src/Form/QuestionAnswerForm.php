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
      if(!$extras['entity'] || !$extras['entity'] instanceof ContentEntityInterface){
          throw new \Exception('Question Answer form requires Entity.');
      }

      $this->entity = $extras['entity'];
      $this->questions = $extras['questions'];

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
      $correctAnswers = 0;
      $totalQuestions = count($this->questions);

      foreach($this->questions as $delta => $question) {
          $selectedAnswers = $form_state->getValue('question_'.$delta);

          $questionCorrectAnswers = $question->getCorrectAnswers();

          if(count($selectedAnswers) > 1){
              array_walk_recursive($selectedAnswers, function($value, $key) use ($selectedAnswers){
                  if(!$value){
                      unset($selectedAnswers[$key]);
                  }
              });
          }else{
              $selectedAnswers = [$selectedAnswers];
          }

          $diffAnswers = array_diff($questionCorrectAnswers, $selectedAnswers);

          if(count($diffAnswers) == 0){
              $correctAnswers += 1;
          }
      }

      $result = $correctAnswers/$totalQuestions * 100;

      $owner = \Drupal::currentUser();
      $questionnaireSubmission = QuestionnaireSubmission::create();
      $questionnaireSubmission->setName($this->entity->label().' by '.$owner->getAccountName());
      $questionnaireSubmission->setOwnerId($owner->id());
      $questionnaireSubmission->setQuestionnaire($this->entity);
      $questionnaireSubmission->setResult($result);
      $questionnaireSubmission->save();

      drupal_set_message(t('Questionnaire successfully submitted. Thank you for finishing @questionnaire!', ['@questionnaire' => $this->entity->label()]));
  }

}
