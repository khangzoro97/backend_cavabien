<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
    }
}
