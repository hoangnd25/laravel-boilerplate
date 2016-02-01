<?php

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="tag")
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Type("integer")
     * @JMS\Groups({"tag_list", "tag_details", "post_details"})
     */
    protected $id;

    /**
     * @ORM\Column(length=5)
     * @JMS\Type("string")
     * @JMS\Groups({"tag_list", "tag_details", "post_details"})
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Model\Post", mappedBy="tags")
     * @JMS\Type("ArrayCollection<App\Model\Post>")
     * @JMS\Groups({"tag_details"})
     */
    protected $posts;

    /**
     * Tag constructor.
     */
    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    function __toString()
    {
        return $this->getName();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param mixed $posts
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;
    }

    /**
     * @param $post
     */
    public function addPost($post){
        $this->posts->add($post);
    }

    /**
     * @param $post
     */
    public function removePost($post){
        $this->posts->removeElement($post);
    }
}