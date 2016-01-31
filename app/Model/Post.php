<?php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="post")
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Type("integer")
     * @JMS\Groups({"post_list", "tag_details", "post_details"})
     */
    protected $id;

    /**
     * @ORM\Column(length=160)
     * @JMS\Type("string")
     * @JMS\Groups({"post_list", "tag_details", "post_details"})
     */
    protected $title;

    /**
     * @ORM\Column()
     * @JMS\Type("string")
     * @JMS\Groups({"post_detail"})
     */
    protected $body;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="posts", cascade={"persist"})
     * @ORM\JoinTable(name="posts_tags")
     * @JMS\Type("ArrayCollection<App\Model\Tag>")
     * @JMS\Groups({"post_details"})
     */
    protected $tags;

    /**
     * Post constructor.
     */
    public function __construct()
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @param $tag
     */
    public function addPost($tag){
        $this->tags->add($tag);
    }

    /**
     * @param $tag
     */
    public function removePost($tag){
        $this->tags->removeElement($tag);
    }
}