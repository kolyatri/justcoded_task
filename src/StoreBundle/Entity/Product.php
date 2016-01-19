<?php

namespace StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use StoreBundle\Entity\Category;

/**
 * @ORM\Entity(repositoryClass="StoreBundle\Entity\Repository\ProductRepository")
 * @ORM\Table(name="product")
 * @ORM\HasLifecycleCallbacks
 * @ExclusionPolicy("all")
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="name",type="string")
     * @Expose
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="Category",inversedBy="products")
     * @ORM\JoinTable(
     *  name="product_category",
     *  joinColumns={
     *      @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     *  }
     * )
     */
    protected $categories;

    /**
     * @ORM\OneToMany(targetEntity="Image", cascade={"persist","remove"}, mappedBy="blog")
     * @Expose
     */
    protected $images;


    /**
     * @var ArrayCollection
     */
    private $uploadedFiles;


    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->uploadedFiles = new ArrayCollection();
    }

    /**
     * @ORM\PreFlush()
     */
    public function upload()
    {
        if ($this->uploadedFiles) {
            foreach ($this->uploadedFiles as $uploadedFile) {
                if ($uploadedFile) {
                    $image = new Image($uploadedFile);
                    $this->getImages()->add($image);
                    $image->setProduct($this);

                    unset($uploadedFile);
                }
            }
        }
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \StoreBundle\Entity\Category $category
     */
    public function addCategory(Category $category)
    {
        if ($this->categories->contains($category)) {
            return;
        }

        $this->categories->add($category);
        $category->addProduct($this);
    }

    /**
     * @param \StoreBundle\Entity\Category $category
     */
    public function removeCategory(Category $category)
    {
        if (!$this->categories->contains($category)) {
            return;
        }

        $this->categories->removeElement($category);
        $category->removeProduct($this);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add image
     *
     * @param \StoreBundle\Entity\Image $image
     *
     * @return Product
     */
    public function addImage(\StoreBundle\Entity\Image $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \StoreBundle\Entity\Image $image
     */
    public function removeImage(\StoreBundle\Entity\Image $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }
}
