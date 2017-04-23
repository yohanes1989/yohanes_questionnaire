<?php

namespace Drupal\yohanes_questionnaire\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\user\UserInterface;
use Drupal\yohanes_questionnaire\Entity\QuestionnaireSubmissionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class QuestionnaireSubmissionController.
 *
 *  Returns responses for Questionnaire submission routes.
 *
 * @package Drupal\yohanes_questionnaire\Controller
 */
class QuestionnaireSubmissionController extends ControllerBase implements ContainerInjectionInterface {

    protected $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
   * Displays a Questionnaire submission  revision.
   *
   * @param int $questionnaire_submission_revision
   *   The Questionnaire submission  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($questionnaire_submission_revision) {
    $questionnaire_submission = $this->entityManager()->getStorage('questionnaire_submission')->loadRevision($questionnaire_submission_revision);
    $view_builder = $this->entityManager()->getViewBuilder('questionnaire_submission');

    return $view_builder->view($questionnaire_submission);
  }

  /**
   * Page title callback for a Questionnaire submission  revision.
   *
   * @param int $questionnaire_submission_revision
   *   The Questionnaire submission  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($questionnaire_submission_revision) {
    $questionnaire_submission = $this->entityManager()->getStorage('questionnaire_submission')->loadRevision($questionnaire_submission_revision);
    return $this->t('Revision of %title from %date', array('%title' => $questionnaire_submission->label(), '%date' => format_date($questionnaire_submission->getRevisionCreationTime())));
  }

  /**
   * Generates an overview table of older revisions of a Questionnaire submission .
   *
   * @param \Drupal\yohanes_questionnaire\Entity\QuestionnaireSubmissionInterface $questionnaire_submission
   *   A Questionnaire submission  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(QuestionnaireSubmissionInterface $questionnaire_submission) {
    $account = $this->currentUser();
    $langcode = $questionnaire_submission->language()->getId();
    $langname = $questionnaire_submission->language()->getName();
    $languages = $questionnaire_submission->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $questionnaire_submission_storage = $this->entityManager()->getStorage('questionnaire_submission');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $questionnaire_submission->label()]) : $this->t('Revisions for %title', ['%title' => $questionnaire_submission->label()]);
    $header = array($this->t('Revision'), $this->t('Operations'));

    $revert_permission = (($account->hasPermission("revert all questionnaire submission revisions") || $account->hasPermission('administer questionnaire submission entities')));
    $delete_permission = (($account->hasPermission("delete all questionnaire submission revisions") || $account->hasPermission('administer questionnaire submission entities')));

    $rows = array();

    $vids = $questionnaire_submission_storage->revisionIds($questionnaire_submission);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\yohanes_questionnaire\QuestionnaireSubmissionInterface $revision */
      $revision = $questionnaire_submission_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->revision_timestamp->value, 'short');
        if ($vid != $questionnaire_submission->getRevisionId()) {
          $link = $this->l($date, new Url('entity.questionnaire_submission.revision', ['questionnaire_submission' => $questionnaire_submission->id(), 'questionnaire_submission_revision' => $vid]));
        }
        else {
          $link = $questionnaire_submission->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->revision_log_message->value, '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => Url::fromRoute('entity.questionnaire_submission.revision_revert', ['questionnaire_submission' => $questionnaire_submission->id(), 'questionnaire_submission_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.questionnaire_submission.revision_delete', ['questionnaire_submission' => $questionnaire_submission->id(), 'questionnaire_submission_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['questionnaire_submission_revisions_table'] = array(
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    );

    return $build;
  }

  public function myResultsPage(UserInterface $user)
  {
      $view = views_embed_view('my_questionnaire_results', 'embed_1', $user->id());

      $element = [
          'results' => [
              '#markup' => $this->renderer->render($view)
          ]
      ];

      return $element;
  }

  public function resultsPage(ContentEntityInterface $node)
  {
      $view = views_embed_view('questionnaire_results', 'embed_1', $node->id());

      $element = [
          'results' => [
              '#markup' => $this->renderer->render($view)
          ]
      ];

      return $element;
  }

  public function resultsPageTitle(ContentEntityInterface $node)
  {
      return t(':questionnaire Results', [':questionnaire' => $node->label()]);
  }

  public function resultsPageAccess(ContentEntityInterface $node, AccountInterface $account)
  {
      $canAccessAllResults = $account->hasPermission('view all questionnaire results');
      $isOwner = $node->getOwnerId() == $account->id();
      $isSupervisor = TRUE;

      if($node->hasField('field_supervisors')){
          $isSupervisor = FALSE;

          $supervisors = $node->get('field_supervisors')->referencedEntities();
          foreach($supervisors as $supervisor){
              if($supervisor->id() == $account->id()){
                  $isSupervisor = TRUE;
                  continue;
              }
          }
      }

      return AccessResult::allowedIf(($canAccessAllResults || $isSupervisor || $isOwner) && $node->hasField('field_questions'));
  }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('renderer')
        );
    }

}
