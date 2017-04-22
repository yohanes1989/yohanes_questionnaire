<?php

namespace Drupal\yohanes_questionnaire\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Question entities.
 *
 * @ingroup yohanes_questionnaire
 */
interface QuestionInterface extends  ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Question name.
   *
   * @return string
   *   Name of the Question.
   */
  public function getName();

  /**
   * Sets the Question name.
   *
   * @param string $name
   *   The Question name.
   *
   * @return \Drupal\yohanes_questionnaire\Entity\QuestionInterface
   *   The called Question entity.
   */
  public function setName($name);

  /**
   * Gets the Question creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Question.
   */
  public function getCreatedTime();

  /**
   * Sets the Question creation timestamp.
   *
   * @param int $timestamp
   *   The Question creation timestamp.
   *
   * @return \Drupal\yohanes_questionnaire\Entity\QuestionInterface
   *   The called Question entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Question published status indicator.
   *
   * Unpublished Question are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Question is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Question.
   *
   * @param bool $published
   *   TRUE to set this Question to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\yohanes_questionnaire\Entity\QuestionInterface
   *   The called Question entity.
   */
  public function setPublished($published);

    /**
     * Returns Answer fields
     *
     * @return FieldItemListInterface
     *  List of QuestionAnswerField fields
     */
    public function getAnswers();

    /**
     * Get Correct Answers
     *
     * @return array
     *  Array of correct answers
     */
    public function getCorrectAnswers();

    /**
     * Returns Question difficulty
     *
     * @return int
     *  Difficulty from 1-5 (Hard)
     */
    public function getDifficulty();

    /**
     * Set Question difficulty
     *
     * @param int $difficulty
     * @return ContentEntityInterface
     *  Question
     */
    public function setDifficulty($difficulty);
}
