<?php

namespace Drupal\Tests\comment\Functional\Hal;

use Drupal\entity_test\Entity\EntityTest;
use Drupal\Tests\comment\Functional\Rest\CommentResourceTestBase;
use Drupal\Tests\hal\Functional\EntityResource\HalEntityNormalizationTrait;
use Drupal\user\Entity\User;

abstract class CommentHalJsonTestBase extends CommentResourceTestBase {

  use HalEntityNormalizationTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = ['hal'];

  /**
   * {@inheritdoc}
   */
  protected static $format = 'hal_json';

  /**
   * {@inheritdoc}
   */
  protected static $mimeType = 'application/hal+json';


  /**
   * {@inheritdoc}
   */
  protected function getExpectedNormalizedEntity() {
    $default_normalization = parent::getExpectedNormalizedEntity();

    $normalization = $this->applyHalFieldNormalization($default_normalization);

    // Because \Drupal\comment\Entity\Comment::getOwner() generates an in-memory
    // User entity without a UUID, we cannot use it.
    $author = User::load($this->entity->getOwnerId());
    $commented_entity = EntityTest::load(1);
    return $normalization + [
      '_links' => [
        'self' => [
          'href' => $this->baseUrl . '/comment/1?_format=hal_json',
        ],
        'type' => [
          'href' => $this->baseUrl . '/rest/type/comment/comment',
        ],
        $this->baseUrl . '/rest/relation/comment/comment/entity_id' => [
          [
            'href' => $this->baseUrl . '/entity_test/1?_format=hal_json',
          ],
        ],
        $this->baseUrl . '/rest/relation/comment/comment/uid' => [
          [
            'href' => $this->baseUrl . '/user/' . $author->id() . '?_format=hal_json',
            'lang' => 'en',
          ],
        ],
      ],
      '_embedded' => [
        $this->baseUrl . '/rest/relation/comment/comment/entity_id' => [
          [
            '_links' => [
              'self' => [
                'href' => $this->baseUrl . '/entity_test/1?_format=hal_json',
              ],
              'type' => [
                'href' => $this->baseUrl . '/rest/type/entity_test/bar',
              ],
            ],
            'uuid' => [
              ['value' => $commented_entity->uuid()]
            ],
          ],
        ],
        $this->baseUrl . '/rest/relation/comment/comment/uid' => [
          [
            '_links' => [
              'self' => [
                'href' => $this->baseUrl . '/user/' . $author->id() . '?_format=hal_json',
              ],
              'type' => [
                'href' => $this->baseUrl . '/rest/type/user/user',
              ],
            ],
            'uuid' => [
              ['value' => $author->uuid()]
            ],
            'lang' => 'en',
          ],
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function getNormalizedPostEntity() {
    return parent::getNormalizedPostEntity() + [
      '_links' => [
        'type' => [
          'href' => $this->baseUrl . '/rest/type/comment/comment',
        ],
      ],
    ];
  }

}
