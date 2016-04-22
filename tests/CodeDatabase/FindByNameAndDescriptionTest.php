<?php

//

namespace CodePress\CodeDatabase\Tests;

use CodePress\CodeDatabase\Tests\AbstractTestCase;
use CodePress\CodeDatabase\Tests\Stub\Models\Category;
use CodePress\CodeDatabase\Tests\Stub\Repository\CategoryRepository;
use CodePress\CodeDatabase\Tests\Stub\Criteria\FindByNameAndDescription;
use CodePress\CodeDatabase\Contracts\CriteriaInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery as m;

class FindByNameAndDescriptionTest extends AbstractTestCase
{

    /**
     * @var \CodePress\CodeDatabase\Repository\CategoryRepository;
     */
    private $repository;

    /**
     *
     * @var FindByNameAndDescription
     */
    private $criteria;

    public function setUp()
    {
        parent::setUp();
        $this->migrate();
        $this->repository = new CategoryRepository();
        $this->criteria = new FindByNameAndDescription('Category 01','Description 01');
        $this->createCategory();
    }

    public function test_if_instanceof_criteria_inteface()
    {
        $this->assertInstanceOf(CriteriaInterface::class, $this->criteria);
    }

    public function test_if_apply_returns_eloquent_builder()
    {
        $class = $this->repository->model();
        $result = $this->criteria->apply(new $class, $this->repository);
        $this->assertInstanceOf(Builder::class, $result);
    }

    public function test_if_apply_returns_data()
    {
        $class = $this->repository->model();
        $result = $this->criteria->apply(new $class, $this->repository)->get()->first();

        $this->assertEquals('Category 01', $result->name);
        $this->assertEquals('Description 01', $result->description);
    }

    // privates
    private function createCategory()
    {
        Category::create([
            'name' => 'Category 01',
            'description' => 'Description 01',
        ]);
        Category::create([
            'name' => 'Category 02',
            'description' => 'Description 02',
        ]);
        Category::create([
            'name' => 'Category 03',
            'description' => 'Description 03',
        ]);
    }

}
