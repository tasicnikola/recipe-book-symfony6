<?php

namespace App\Entity;

use App\Entity\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\RecipeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'recipes')]
class Recipe
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 100)]
    private string $title;

    #[ORM\Column(length: 255)]
    private string $imageUrl;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User")]
    #[ORM\JoinColumn(name: 'user', referencedColumnName: 'id')]
    private User $user;

    #[ORM\OneToMany(targetEntity: "App\Entity\RecipeIngredient", mappedBy: "recipe", fetch:"EXTRA_LAZY", orphanRemoval: true)]
    private $recipeIngredient;

    public function __construct()
    {
        $this->recipeIngredient = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
