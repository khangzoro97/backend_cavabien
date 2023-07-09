<?php

namespace App\Repositories;

use App\Models\Admin;

class AdminRepository extends BaseRepository implements AdminRepositoryInterface
{
    protected $model;

    public function __construct(Admin $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
    }
}
