<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $product_count;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="category")
     */
    private $product_relation;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="categoryParentRelation")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Category::class, mappedBy="category")
     */
    private $categoryParentRelation;

    public function __construct()
    {
        $this->product_relation = new ArrayCollection();
        $this->categoryParentRelation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getProductCount(): ?int
    {
        return $this->product_count;
    }

    public function setProductCount(int $product_count): self
    {
        $this->product_count = $product_count;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProductRelation(): Collection
    {
        return $this->product_relation;
    }

    public function addProductRelation(Product $productRelation): self
    {
        if (!$this->product_relation->contains($productRelation)) {
            $this->product_relation[] = $productRelation;
            $productRelation->setCategory($this);
        }

        return $this;
    }

    public function removeProductRelation(Product $productRelation): self
    {
        if ($this->product_relation->removeElement($productRelation)) {
            // set the owning side to null (unless already changed)
            if ($productRelation->getCategory() === $this) {
                $productRelation->setCategory(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?self
    {
        return $this->category;
    }

    public function setCategory(?self $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getCategoryParentRelation(): Collection
    {
        return $this->categoryParentRelation;
    }

    public function addCategoryParentRelation(self $categoryParentRelation): self
    {
        if (!$this->categoryParentRelation->contains($categoryParentRelation)) {
            $this->categoryParentRelation[] = $categoryParentRelation;
            $categoryParentRelation->setCategory($this);
        }

        return $this;
    }

    public function removeCategoryParentRelation(self $categoryParentRelation): self
    {
        if ($this->categoryParentRelation->removeElement($categoryParentRelation)) {
            // set the owning side to null (unless already changed)
            if ($categoryParentRelation->getCategory() === $this) {
                $categoryParentRelation->setCategory(null);
            }
        }

        return $this;
    }
}
