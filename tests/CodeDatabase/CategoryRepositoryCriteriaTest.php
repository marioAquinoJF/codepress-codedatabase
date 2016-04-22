<?php

namespace CodePress\CodeDatabase\Tests;

use CodePress\CodeDatabase\Tests\AbstractTestCase;
use CodePress\CodeDatabase\Tests\Stub\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use CodePress\CodeDatabase\Tests\Stub\Repository\CategoryRepository;
//use CodePress\CodeCategory\Repositories\CategoryRepository;
use CodePress\CodeDatabase\Contracts\CriteriaCollection;
use CodePress\CodeDatabase\Contracts\CriteriaInterface;
use CodePress\CodeDatabase\Tests\Stub\Criteria\FindByNameAndDescription;
use CodePress\CodeDatabase\Tests\Stub\Criteria\FindByDescription;
use CodePress\CodeDatabase\Tests\Stub\Criteria\FindByName;
use CodePress\CodeDatabase\Tests\Stub\Criteria\OrderDescByName;
use CodePress\CodeDatabase\Tests\Stub\Criteria\OrderDescById;
use Illuminate\Database\Query\Builder;
use Mockery as m;

class CategoryRepositoryCriteriaTest extends AbstractTestCase
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
        $this->createCategory();
    }

    public function test_if_instanceof_criteriacollection()
    {
        $this->assertInstanceOf(CriteriaCollection::class, $this->repository);
    }

    public function test_if_can_getcriteriacollection()
    {
        $result = $this->repository->getCriteriaCollection();
        $this->assertCount(0, $result);
    }

    public function test_if_can_addcriteria()
    {

        $mockCriteria = m::mock(CriteriaInterface::class);
        $result = $this->repository->addCriteria($mockCriteria);

        $this->assertInstanceOf(CriteriaCollection::class, $result);
        $this->assertCount(1, $result->getCriteriaCollection());
    }

    public function test_if_can_getbycriteria()
    {

        $criteria = new FindByNameAndDescription('category 01', 'Description 01');
        $repository = $this->repository->getByCriteria($criteria);
        $this->assertInstanceOf(CriteriaCollection::class, $repository);

        $result = $repository->all();
        $this->assertCount(1, $result);
        $result = $result->first();
        $this->assertEquals('category 01', $result->name);
        $this->assertEquals('Description 01', $result->description);
    }

    public function test_if_can_applycriteria()
    {
        $this->createCategoryDescription();
        $criteria01 = new FindByDescription('Description');
        $criteria02 = new OrderDescByName();

        $this->repository->addCriteria($criteria01)
                ->addCriteria($criteria02);
        $repository = $this->repository->applyCriteria();
        $this->assertInstanceOf(CriteriaCollection::class, $repository);

        $result = $repository->all();
        $this->assertCount(3, $result);
        $this->assertEquals('category um', $result[0]->name);
        $this->assertEquals('category dois', $result[1]->name);
    }

    public function test_if_can_list_all_categories_with_criteria()
    {
        $this->createCategoryDescription();

        $criteria01 = new FindByDescription('Description');
        $criteria02 = new OrderDescByName();

        $this->repository->addCriteria($criteria01)
                ->addCriteria($criteria02);
        $result = $this->repository->all();
        $this->assertCount(3, $result);
        $this->assertEquals('category um', $result[0]->name);
        $this->assertEquals('category dois', $result[1]->name);
    }

    /**
     * @expectedException \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function test_if_can_find_category_with_criteria_and_exception()
    {
        $this->createCategoryDescription();

        $criteria01 = new FindByDescription('Description');
        $criteria02 = new FindByName('category dois');

        $this->repository->addCriteria($criteria01)
                ->addCriteria($criteria02);
        $this->repository->find(5);
    }

    public function test_if_can_find_category_with_criteria()
    {
        $this->createCategoryDescription();

        $criteria01 = new FindByDescription('Description');
        $criteria02 = new FindByName('category um');

        $this->repository->addCriteria($criteria01)
                ->addCriteria($criteria02);
        $result = $this->repository->find(5);
        $this->assertEquals($result->name, 'category um');
        $this->assertEquals($result->description, 'Description');
    }

    public function test_if_can_find_by_categories_with_criteria()
    {
        $this->createCategoryDescription();

        $criteria02 = new FindByName('category dois');
        $criteria01 = new OrderDescById();

        $this->repository->addCriteria($criteria01)
                ->addCriteria($criteria02);
        $result = $this->repository->findBy('description', 'Description');

        $this->assertCount(2, $result);
        $this->assertEquals($result[0]->id, 6);
        $this->assertEquals($result[0]->name, 'category dois');
        $this->assertEquals($result[1]->id, 4);
        $this->assertEquals($result[1]->name, 'category dois');
    }

    public function test_if_can_ignore_criteria()
    {
        $reflectionClass = new \ReflectionClass($this->repository);
        $reflectionProperty = $reflectionClass->getProperty('isIgnoredCriteria');
        $reflectionProperty->setAccessible(true);
        $result = $reflectionProperty->getValue($this->repository);
        $this->assertFalse($result);

        $this->repository->ignoreCriteria();
        $result = $reflectionProperty->getValue($this->repository);
        $this->assertTrue($result);

        $this->repository->ignoreCriteria(true);
        $result = $reflectionProperty->getValue($this->repository);
        $this->assertTrue($result);

        $this->repository->ignoreCriteria(false);
        $result = $reflectionProperty->getValue($this->repository);
        $this->assertFalse($result);

        $this->assertInstanceOf(CategoryRepository::class, $this->repository->ignoreCriteria(false));
    }

    public function test_if_can_ignore_applycriteria()
    {
        $this->createCategoryDescription();
        $criteria01 = new FindByDescription('Description');
        $criteria02 = new OrderDescByName();

        $this->repository
                ->addCriteria($criteria01)
                ->addCriteria($criteria02);

        $this->repository->ignoreCriteria();
        $this->repository->applyCriteria();
        $reflectionClass = new \ReflectionClass($this->repository);
        $reflectionProperty = $reflectionClass->getProperty('model');
        $reflectionProperty->setAccessible(true);
        $result = $reflectionProperty->getValue($this->repository);
        $this->assertInstanceOf(Category::class, $result);

        $this->repository->ignoreCriteria(false);
        $repository = $this->repository->applyCriteria();
        $this->assertInstanceOf(CriteriaCollection::class, $repository);

        $result = $repository->all();
        $this->assertCount(3, $result);
        $this->assertEquals('category um', $result[0]->name);
        $this->assertEquals('category dois', $result[1]->name);
    }

    public function test_if_can_clear_criterias()
    {
        $this->createCategoryDescription();

        $criteria02 = new FindByName('category dois');
        $criteria01 = new OrderDescById();

        $this->repository->addCriteria($criteria01)
                ->addCriteria($criteria02);
        
        $this->assertInstanceOf(CategoryRepository::class, $this->repository->clearCriteria());
        $result = $this->repository->findBy('description', 'Description');

        $this->assertCount(3, $result);

        $reflectionClass = new \ReflectionClass($this->repository);
        $reflectionProperty = $reflectionClass->getProperty('model');
        $reflectionProperty->setAccessible(true);
        $result = $reflectionProperty->getValue($this->repository);
        $this->assertInstanceOf(Category::class, $result);
    }

    // privates
    public function createCategoryDescription()
    {
        Category::create([
            'name' => 'category dois',
            'description' => 'Description',
        ]);
        Category::create([
            'name' => 'category um',
            'description' => 'Description',
        ]);

        Category::create([
            'name' => 'category dois',
            'description' => 'Description',
        ]);
    }

    private function createCategory()
    {
        Category::create([
            'name' => 'category 01',
            'description' => 'Description 01',
        ]);
        Category::create([
            'name' => 'category 02',
            'description' => 'Description 02',
        ]);
        Category::create([
            'name' => 'category 03',
            'description' => 'Description 03',
        ]);
    }

}
