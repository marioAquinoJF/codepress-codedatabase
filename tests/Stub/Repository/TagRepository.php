<?php

namespace CodePress\CodeDatabase\Tests\Stub\Repository;

use CodePress\CodeDatabase\AbstractRepository;
use CodePress\CodeDatabase\Tests\Stub\Models\Tag;

class TagRepository extends AbstractRepository
{

    public function model()
    {
        return Tag::class;
    }

}
