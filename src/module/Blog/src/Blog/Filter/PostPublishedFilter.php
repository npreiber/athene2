<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013-2014 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Blog\Filter;

use Blog\Entity\PostInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Zend\Filter\Exception;
use Zend\Filter\FilterInterface;

class PostPublishedFilter implements FilterInterface
{
    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        $result = [];

        if (!$value instanceof Collection) {
            throw new Exception\RuntimeException(sprintf(
                'Expected instance of Collection but got %s.',
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        foreach ($value as $post) {
            if (!$post instanceof PostInterface) {
                throw new Exception\RuntimeException(sprintf(
                    'Expected instance of PostInterface but got %s.',
                    is_object($post) ? get_class($post) : gettype($post)
                ));
            }
            if (!$post->isTrashed() && $post->isPublished()) {
                $result[] = $post;
            }
        }

        return new ArrayCollection($result);
    }
}
