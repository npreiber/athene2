<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Entity\Entity;

use Doctrine\ORM\Mapping as ORM;
use Link\Entity\LinkableInterface;
use Link\Entity\LinkInterface;

/**
 * An
 * entity
 * link.
 *
 * @ORM\Entity
 * @ORM\Table(name="entity_link")
 */
class EntityLink implements LinkInterface
{
    use \Type\Entity\TypeAwareTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="Entity",
     * inversedBy="parents")
     * @ORM\JoinColumn(name="child_id",
     * referencedColumnName="id")
     */
    public $child;

    /**
     * @ORM\ManyToOne(targetEntity="Entity",
     * inversedBy="children")
     * @ORM\JoinColumn(name="parent_id",
     * referencedColumnName="id")
     */
    public $parent;

    /**
     * @ORM\Column(name="`order`", type="integer")
     */
    public $order;

    public function getPosition()
    {
        return $this->order;
    }

    public function setPosition($position)
    {
        $this->order = $position;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getChild()
    {
        return $this->child;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setChild(LinkableInterface $child)
    {
        $this->child = $child;
    }

    public function setParent(LinkableInterface $parent)
    {
        $this->parent = $parent;
    }
}
