<?php

namespace CodePress\CodeDatabase\Tests\Stub\Criteria;

use CodePress\CodeDatabase\Contracts\CriteriaInterface;
use CodePress\CodeDatabase\Contracts\RepositoryInterface;

class FindByDescription implements CriteriaInterface
{

    private $description;

    function __construct($description)
    {
        $this->description = $description;
    }

    public function apply($model, RepositoryInterface $repository)
    {
        return $model->where('description', $this->description);
    }

}
