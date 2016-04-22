<?php

namespace CodePress\CodeDatabase\Tests;

use CodePress\CodeDatabase\Tests\AbstractTestCase;
use CodePress\CodeDatabase\Tests\Stub\Models\Tag;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use CodePress\CodeDatabase\Tests\Stub\Repository\TagRepository;
use Mockery as m;


class TagRepositoryTest extends AbstractTestCase
{

    /**
     * @var \CodePress\CodeDatabase\Repository\TagRepository;
     */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->migrate();
        $this->repository = new TagRepository();
        $this->createCategory();
    }

    public function test_can_model()
    {
        $this->assertEquals(Tag::class, $this->repository->model());
    }

    public function test_can_make_model()
    {
        $result = $this->repository->makeModel();
        $this->assertInstanceOf(Tag::class, $result);

        $reflectionClass = new \ReflectionClass($this->repository);
        $reflectionProperty = $reflectionClass->getProperty('model');
        $reflectionProperty->setAccessible(true);

        $result = $reflectionProperty->getValue($this->repository);
        $this->assertInstanceOf(Tag::class, $result);
    }

    public function test_can_make_model_in_constructor()
    {
        $reflectionClass = new \ReflectionClass($this->repository);
        $reflectionProperty = $reflectionClass->getProperty('model');
        $reflectionProperty->setAccessible(true);

        $result = $reflectionProperty->getValue($this->repository);
        $this->assertInstanceOf(Tag::class, $result);
    }

    public function test_if_can_list_all_tags()
    {
        $result = $this->repository->all();
        $this->assertCount(3, $result);
        $this->assertEquals('Tag 01',$result[0]->name);

    }

    public function test_if_can_create_tag()
    {
        $result = $this->repository->create([
            'name' => 'Tag 04'
        ]);

        $this->assertInstanceOf(Tag::class, $result);
        $this->assertEquals('Tag 04', $result->name);

        $result = Tag::find(4);
        $this->assertEquals('Tag 04', $result->name);
    }

    public function test_if_can_update_tag()
    {
        $result = $this->repository->update([
            'name' => 'Tag Atualizada'
                ], 1);

        $this->assertInstanceOf(Tag::class, $result);
        $this->assertEquals('Tag Atualizada', $result->name);

        $result = Tag::find(1);
        $this->assertEquals('Tag Atualizada', $result->name);
    }

    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function test_if_can_update_tag_fail()
    {
        $result = $this->repository->update([
            'name' => 'Tag Atualizada'
                ], 10);
    }

    public function test_if_can_delete_tag()
    {
        $result = $this->repository->delete(1);
        $tag = Tag::all();
        $this->assertCount(2, $tag);
        $this->assertTrue($result);
    }

    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function test_if_can_delete_tag_fail()
    {
        $this->repository->delete(10);
    }

    public function test_if_can_find_tag()
    {
        $result = $this->repository->find(2);
        $this->assertInstanceOf(Tag::class, $result);
    }

    public function test_if_can_find_tag_with_columns()
    {
        $result = $this->repository->find(2, ['name']);
        $this->assertInstanceOf(Tag::class, $result);
    }

    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function test_if_can_find_tag_fail()
    {
        $this->repository->delete(10);
    }

    public function test_if_can_find_categories()
    {
        $result = $this->repository->findBy('name', 'Tag 03');
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Tag::class, $result[0]);
        $this->assertEquals('Tag 03', $result[0]->name);

        $result = $this->repository->findBy('name', 'Tag 10');
        $this->assertCount(0, $result);

        $result = $this->repository->findBy('name', 'Tag 03', ['name']);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Tag::class, $result[0]);
    }

    // privates
    private function createCategory()
    {
        Tag::create([
            'name' => 'Tag 01'
        ]);
        Tag::create([
            'name' => 'Tag 02'
        ]);
        Tag::create([
            'name' => 'Tag 03'
        ]);
    }

}
