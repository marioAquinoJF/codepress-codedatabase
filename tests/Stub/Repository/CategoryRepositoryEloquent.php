<?php

namespace CodePress\CodeDatabase\Tests\Stub\Repository;

use CodePress\CodeDatabase\AbstractRepository;
use CodePress\CodeDatabase\Tests\Stub\Models\Category;

class CategoryRepositoryEloquent extends AbstractRepository implements CategoryRepositoryInterface
{

    public function model()
    {
        return Category::class;
    }

}
