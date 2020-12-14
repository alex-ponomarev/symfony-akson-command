<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;


/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @OA\Property(description="The unique identifier of the user.")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @OA\Property(type="string", maxLength=255)
     */
    private $name;
    
    /**
     * @ORM\Column(type="integer")
     * @OA\Property(type="integer")
     */
    private $product_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @OA\Property(type="integer")
     */
    private $category;


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

    public function getCategory(): ?int
    {
        return $this->category;
    }

    public function setCategory(?int $category): self
    {
        $this->category = $category;

        return $this;
    }

}
