<?php

namespace Drupal\yohanes_questionnaire\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Question entity.
 *
 * @ingroup yohanes_questionnaire
 *
 * @ContentEntityType(
 *   id = "question",
 *   label = @Translation("Question"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\yohanes_questionnaire\QuestionListBuilder",
 *     "views_data" = "Drupal\yohanes_questionnaire\Entity\QuestionViewsData",
 *     "translation" = "Drupal\yohanes_questionnaire\QuestionTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\yohanes_questionnaire\Form\QuestionForm",
 *       "add" = "Drupal\yohanes_questionnaire\Form\QuestionForm",
 *       "edit" = "Drupal\yohanes_questionnaire\Form\QuestionForm",
 *       "delete" = "Drupal\yohanes_questionnaire\Form\QuestionDeleteForm",
 *     },
 *     "access" = "Drupal\yohanes_questionnaire\QuestionAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\yohanes_questionnaire\QuestionHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "question",
 *   data_table = "question_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer question entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/content/question/{question}",
 *     "add-form" = "/admin/content/question/add",
 *     "edit-form" = "/admin/content/question/{question}/edit",
 *     "delete-form" = "/admin/content/question/{question}/delete",
 *     "collection" = "/admin/content/question",
 *   },
 *   field_ui_base_route = "question.settings"
 * )
 */
class Question extends ContentEntityBase implements QuestionInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

    /**
     * {@inheritdoc}
     */
    public function getAnswers()
    {
        return $this->get('field_answers');
    }

    /**
     * {@inheritdoc}
     */
    public function getCorrectAnswers()
    {
        $correctAnswers = [];

        foreach($this->getAnswers() as $delta => $answer){
            if($answer->get('is_correct')->getValue()){
                $correctAnswers[] = $delta + 1;
            }
        }

        return $correctAnswers;
    }

    /**
     * {@inheritdoc}
     */
    public function getDifficulty()
    {
        return $this->get('field_difficulty');
    }

    /**
     * {@inheritdoc}
     */
    public function setDifficulty($difficulty)
    {
        $this->set('field_difficulty', $difficulty);

        return $this;
    }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Question entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'region' => 'hidden',
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Question'))
      ->setSettings(array(
        'max_length' => 255,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -4,
      ))
        ->setRequired(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Question is published.'))
      ->setDefaultValue(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['field_answers'] = BaseFieldDefinition::create('question_answer_field')
        ->setLabel(t('Answers'))
        ->setDescription(t('Answers that should be selected to answer a Question'))
        ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
        ->setRequired(TRUE)
        ->setDisplayOptions('form', array(
            'region' => 'content',
            'type' => 'question_answer_widget',
            'weight' => 1,
        ))
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE);

      $fields['field_difficulty'] = BaseFieldDefinition::create('list_integer')
          ->setLabel(t('Difficulty'))
          ->setRequired(TRUE)
          ->setSetting('allowed_values', [
              1 => t('1 - Easy'),
              2 => t('2'),
              3 => t('3 - Moderate'),
              4 => t('4'),
              5 => t('5 - Hard'),
          ])
          ->setDisplayOptions('form', array(
              'region' => 'content',
              'type' => 'options_select',
              'weight' => 3,
          ))
          ->setDisplayConfigurable('form', TRUE)
          ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }
}
