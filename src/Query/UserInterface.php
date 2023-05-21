<?php

declare(strict_types=1);

namespace App\Query;

use App\DTO\Collection\Users;
use App\DTO\UserDTO;

interface UserInterface
{
    public function getAll(): Users;

    public function getById(int $id): ?UserDTO;

    public function getByEmail(string $email): ?UserDTO;
}
