<?php

declare(strict_types=1);

namespace App\Request\Recipe;

use App\DTO\RequestParams\RecipeParams;
use App\Request\Field\Title;
use App\Request\Field\ImageUrl;
use App\Request\Field\Description;
use App\Request\Field\Guid;
use App\Request\Field\Ingredients;
use App\Request\Field\UserId;

interface Recipe extends Guid, Title, ImageUrl, Description, UserId, Ingredients
{
    public function params(): RecipeParams;
}
