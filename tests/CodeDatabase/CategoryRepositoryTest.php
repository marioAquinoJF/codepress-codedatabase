<?php

namespace CodePress\CodeDatabase\Tests;

use CodePress\CodeDatabase\Tests\AbstractTestCase;
use CodePress\CodeDatabase\Tests\Stub\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use CodePress\CodeDatabase\Tests\Stub\Repository\CategoryRepository;
use Mockery as m;

class CategoryRepositoryTest extends AbstractTestCase
{

    /**
     * @var \CodePress\CodeDatabase\Repository\CategoryRepository;
     */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->migrate();
        $this->repository = new CategoryRepository();
        $this->createCategory();
    }

    public function test_can_model()
    {
        $this->assertEquals(Category::class, $this->repository->model());
    }

    public function test_can_make_model()
    {
        $result = $this->repository->makeModel();
        $this->assertInstanceOf(Category::class, $result);

        $reflectionClass = new \ReflectionClass($this->repository);
        $reflectionProperty = $reflectionClass->getProperty('model');
        $reflectionProperty->setAccessible(true);

        $result = $reflectionProperty->getValue($this->repository);
        $this->assertInstanceOf(Category::class, $result);
    }

    public function test_can_make_model_in_constructor()
    {
        $reflectionClass = new \ReflectionClass($this->repository);
        $reflectionProperty = $reflectionClass->getProperty('model');
        $reflectionProperty->setAccessible(true);

        $result = $reflectionProperty->getValue($this->repository);
        $this->assertInstanceOf(Category::class, $result);
    }

    public function test_if_can_list_all_categories()
    {
        $result = $this->repository->all();
        $this->assertCount(3, $result);
        $this->assertNotNull($result[0]->description);

        $result = $this->repository->all(['name']);
        $this->assertNull($result[0]->description);
    }

    public function test_if_can_create_category()
    {
        $result = $this->repository->create([
            'name' => 'Category 04',
            'description' => 'Description 04',
        ]);

        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals('Category 04', $result->name);
        $this->assertEquals('Description 04', $result->description);

        $result = Category::find(4);
        $this->assertEquals('Category 04', $result->name);
        $this->assertEquals('Description 04', $result->description);
    }

    public function test_if_can_update_category()
    {
        $result = $this->repository->update([
            'name' => 'Category Atualizada',
            'description' => 'Description Atualizada'
                ], 1);

        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals('Category Atualizada', $result->name);
        $this->assertEquals('Description Atualizada', $result->description);

        $result = Category::find(1);
        $this->assertEquals('Category Atualizada', $result->name);
        $this->assertEquals('Description Atualizada', $result->description);
    }

    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function test_if_can_update_category_fail()
    {
        $result = $this->repository->update([
            'name' => 'Category Atualizada',
            'description' => 'Description Atualizada'
                ], 10);
    }

    public function test_if_can_delete_category()
    {
        $result = $this->repository->delete(1);
        $categories = Category::all();
        $this->assertCount(2, $categories);
        $this->assertTrue($result);
    }

    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function test_if_can_delete_category_fail()
    {
        $this->repository->delete(10);
    }

    public function test_if_can_find_category()
    {
        $result = $this->repository->find(2);
        $this->assertInstanceOf(Category::class, $result);
    }

    public function test_if_can_find_category_with_columns()
    {
        $result = $this->repository->find(2, ['name']);
        $this->assertInstanceOf(Category::class, $result);
        $this->assertNull($result->description);
    }

    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function test_if_can_find_category_fail()
    {
        $this->repository->delete(10);
    }

    public function test_if_can_find_categories()
    {
        $result = $this->repository->findBy('name', 'category 03');
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Category::class, $result[0]);
        $this->assertEquals('category 03', $result[0]->name);

        $result = $this->repository->findBy('name', 'category 10');
        $this->assertCount(0, $result);

        $result = $this->repository->findBy('name', 'category 03', ['name']);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Category::class, $result[0]);
        $this->assertNull($result[0]->description);
    }

    // privates
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
