<?php

namespace App\Service;

use App\DTO\Collection\Recipes;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Recipe;
use App\DTO\RecipeDTO;
use App\DTO\RequestParams\IngredientParams;
use App\DTO\RequestParams\RecipeParams;
use App\Entity\Ingredient;
use App\Repository\RecipeRepository;
use App\Query\RecipeInterface;
use App\Exception\NotFound\RecipeNotFoundException;
use App\Exception\NotFound\UserNotFoundException;
use App\Repository\IngredientRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;

class RecipeService
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly RecipeRepository $repository,
        private readonly RecipeInterface $query,
        private readonly UserRepository $userRepository,
        private readonly IngredientRepository $ingredientRepository,
    ) {
    }

    public function getAll(): ?Recipes
    {
        return $this->query->getAll();
    }

    public function get(string $guid): ?RecipeDTO
    {
        $recipe = $this->query->get($guid);

        if (null === $recipe) {
            throw new  RecipeNotFoundException($guid);
        }

        return $recipe;
    }

    public function create(RecipeParams $params): int
    {
        $recipe = $this->repository->getEntityInstance();
        $user = $this->userRepository->find($params->user);

        if (null === $user) {
            throw new UserNotFoundException($params->user);
        }
        $ingredients = new ArrayCollection();

        foreach ($params->ingredients->params as $ingredientParams) {
            $ingredient = $this->ingredientRepository->getEntityInstance();
            $ingredient->update($ingredientParams, $recipe);
            $ingredients->add($ingredient);
        }

        $recipe->setIngredients($ingredients);
        $recipe->update($params, $user);
        $this->repository->save($recipe);

        return $recipe->getGuid();
    }

    public function delete(string $guid): void
    {
        $recipe = $this->getRecipeEntity($guid);

        $this->repository->remove($recipe);
    }

    public function update(string $guid, RecipeParams $params): void
    {
        $recipe = $this->getRecipeEntity($guid);
        $user = $this->userRepository->find($params->user);

        foreach ($params->ingredients->params as $ingredientParams) {
            $ingredient = $recipe->getIngredients()->findFirst(fn (int $index, Ingredient $ingredient) => $ingredient->getName() === $ingredientParams->name);

            if (!$ingredient) {
                $ingredient = $this->ingredientRepository->getEntityInstance();
                $recipe->addIngredient($ingredient);
            }

            $ingredient->update($ingredientParams, $recipe);
        }

        $recipe->update($params, $user);
        $recipe->syncIngredients(array_map(fn (IngredientParams $ingredientParams) => $ingredientParams->name, $params->ingredients->params));

        $this->repository->save($recipe);
    }

    private function getRecipeEntity(string $guid): Recipe
    {
        $recipe = $this->repository->find($guid);

        if (null === $recipe) {
            throw new RecipeNotFoundException($guid);
        }

        return $recipe;
    }
}
