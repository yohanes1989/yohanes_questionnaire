<?php

namespace Drupal\yohanes_questionnaire\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Questionnaire submission entity.
 *
 * @ingroup yohanes_questionnaire
 *
 * @ContentEntityType(
 *   id = "questionnaire_submission",
 *   label = @Translation("Questionnaire submission"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\yohanes_questionnaire\QuestionnaireSubmissionListBuilder",
 *     "views_data" = "Drupal\yohanes_questionnaire\Entity\QuestionnaireSubmissionViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\yohanes_questionnaire\Form\QuestionnaireSubmissionForm",
 *       "add" = "Drupal\yohanes_questionnaire\Form\QuestionnaireSubmissionForm",
 *       "edit" = "Drupal\yohanes_questionnaire\Form\QuestionnaireSubmissionForm",
 *       "delete" = "Drupal\yohanes_questionnaire\Form\QuestionnaireSubmissionDeleteForm",
 *     },
 *     "access" = "Drupal\yohanes_questionnaire\QuestionnaireSubmissionAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\yohanes_questionnaire\QuestionnaireSubmissionHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "questionnaire_submission",
 *   admin_permission = "administer questionnaire submission entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode"
 *   },
 *   links = {
 *     "canonical" = "/admin/content/questionnaire_submission/{questionnaire_submission}",
 *     "add-form" = "/admin/content/questionnaire_submission/add",
 *     "edit-form" = "/admin/content/questionnaire_submission/{questionnaire_submission}/edit",
 *     "delete-form" = "/admin/content/questionnaire_submission/{questionnaire_submission}/delete",
 *     "collection" = "/admin/content/questionnaire_submission",
 *   },
 *   field_ui_base_route = "questionnaire_submission.settings"
 * )
 */
class QuestionnaireSubmission extends ContentEntityBase implements QuestionnaireSubmissionInterface {

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
    return $this->getQuestionnaire()->label();
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
  public function getResult() {
    $this->get('result')->getValue();
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setResult($result) {
    $this->set('result', $result);

    return $this;
  }

    /**
     * {@inheritdoc}
     */
  public function getQuestionnaire()
  {
      return $this->get('questionnaire_id')->getEntity();
  }

    /**
     * {@inheritdoc}
     */
  public function setQuestionnaire($questionnaire)
  {
      $this->set('questionnaire_id', $questionnaire->id());

      return $this;
  }

    /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

      $fields['name'] = BaseFieldDefinition::create('string')
          ->setLabel(t('Name'))
          ->setDisplayOptions('view', array(
              'label' => 'above',
              'type' => 'string',
              'weight' => -4,
          ))
          ->setDisplayOptions('form', array(
              'type' => 'string_textfield',
              'weight' => -4,
          ))
          ->setDisplayConfigurable('form', TRUE)
          ->setDisplayConfigurable('view', TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Submitted by'))
      ->setDescription(t('The user ID of author of the Questionnaire submission entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

      $fields['questionnaire_id'] = BaseFieldDefinition::create('entity_reference')
          ->setLabel(t('Questionnaire'))
          ->setDescription(t('The Questionnaire this submission belongs to.'))
          ->setSetting('target_type', 'node')
          ->setSetting('handler', 'default')
          ->setSetting('handler_settings', ['target_bundles' => ['questionnaire']])
          ->setDisplayOptions('view', array(
              'label' => 'above',
              'type' => 'entity_reference_label',
              'weight' => 10,
              'settings' => [
                  'link' => TRUE
              ]
          ))
          ->setDisplayConfigurable('view', TRUE);

  $fields['result'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Result'))
      ->setSettings(array(
          'min' => 0,
          'precision' => 5
      ))
      ->setDisplayOptions('view', array(
          'label' => 'above',
          'type' => 'number_decimal',
          'weight' => 10,
      ))
      ->setDisplayOptions('form', array(
          'type' => 'number',
          'weight' => 10,
      ))
      ->setRequired(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
