<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @OA\Property(readOnly="true")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @OA\Property(type="string", maxLength=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @OA\Property(type="string", maxLength=255)
     */
    private $sku;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @OA\Property(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @OA\Property(type="string", maxLength=255)
     */
    private $description;

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

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(?string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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
