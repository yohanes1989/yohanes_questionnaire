<?php

namespace Drupal\yohanes_questionnaire\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Questionnaire submission entities.
 *
 * @ingroup yohanes_questionnaire
 */
interface QuestionnaireSubmissionInterface extends  ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Questionnaire submission name.
   *
   * @return string
   *   Name of the Questionnaire submission.
   */
  public function getName();

  /**
   * Sets the Questionnaire submission name.
   *
   * @param string $name
   *   Name of the Questionnaire submission.
   * @return ContentEntityInterface
   *    Questionnaire Submission
   */
  public function setName($name);

  /**
   * Gets the Questionnaire submission creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Questionnaire submission.
   */
  public function getCreatedTime();

  /**
   * Sets the Questionnaire submission creation timestamp.
   *
   * @param int $timestamp
   *   The Questionnaire submission creation timestamp.
   *
   * @return \Drupal\yohanes_questionnaire\Entity\QuestionnaireSubmissionInterface
   *   The called Questionnaire submission entity.
   */
  public function setCreatedTime($timestamp);

    /**
     * Get the Questionnaire submission result
     *
     * @return float
     *  Questionnaire result
     */
  public function getResult();

    /**
     * Set the Questionnaire submission result
     *
     * @param float $result
     *  Questionnaire result
     * @return QuestionnaireSubmissionInterface
     *  Questionnaire submission entity
     */
  public function setResult($result);

  /**
     * Get the Questionnaire
     *
     * @return float
     *  Questionnaire result
     */
  public function getQuestionnaire();

    /**
     * Set the Questionnaire
     *
     * @param ContentEntityInterface $questionnaire
     *  Questionnaire
     * @return QuestionnaireSubmissionInterface
     *  Questionnaire submission entity
     */
  public function setQuestionnaire($questionnaire);
}
