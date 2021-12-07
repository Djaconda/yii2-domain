<?php

namespace PHPKitchen\Domain\Specs\Unit\Data;

use PHPKitchen\Domain\Data\EntitiesProvider;
use PHPKitchen\Domain\Specs\Base\Spec;
use PHPKitchen\Domain\Specs\Unit\Stubs\Models\Dummy\DummyEntity;
use PHPKitchen\Domain\Specs\Unit\Stubs\Models\Dummy\DummyQuery;
use PHPKitchen\Domain\Specs\Unit\Stubs\Models\Dummy\DummyRecord;
use PHPKitchen\Domain\Specs\Unit\Stubs\Models\Dummy\DummyRepository;

/**
 * Unit test for {@link EntitiesProvider}
 *
 * @coversDefaultClass \PHPKitchen\Domain\data\EntitiesProvider
 *
 * @package tests\data
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class EntityProviderTest extends Spec {
    public const STUBBED_RECORDS_COUNT = 5;

    /**
     * @test
     * @covers ::getModels
     */
    public function getDataAsEntitiesBehavior(): void {
        $dataProvider = $this->createDatProviderWithStubbedRecordsData();
        $count = $dataProvider->getCount();
        $I = $this->tester;
        $I->expectThat('data provider correctly calculates entities number');
        $I->see($count)
          ->isEqualTo(self::STUBBED_RECORDS_COUNT);
        $models = $dataProvider->getModels();
        $I->expectThat('data provider correctly calculates entities number');
        $I->seeArray($models)
          ->countIsEqualToCountOf(self::STUBBED_RECORDS_COUNT);
        foreach ($models as $model) {
            $I->expectThat('data provider have converted record to entity');
            $I->seeObject($model)
              ->isInstanceOf(DummyEntity::class);
        }
    }

    /**
     * @test
     * @covers ::getModels
     */
    public function getDataAsArray(): void {
        $dataProvider = $this->createDatProviderWithStubbedArrayData();
        $count = $dataProvider->getCount();
        $I = $this->tester;
        $I->expectThat('data provider correctly calculates entities number');
        $I->see($count)
          ->isEqualTo(self::STUBBED_RECORDS_COUNT);
        $models = $dataProvider->getModels();
        $I->expectThat('data provider correctly calculates entities number');
        $I->seeArray($models)
          ->countIsEqualToCountOf(self::STUBBED_RECORDS_COUNT);
        foreach ($models as $model) {
            $I->expectThat('data provider have converted record to entity');
            $I->see($model)
              ->isArray();
        }
    }

    protected function createDatProviderWithStubbedRecordsData(): EntitiesProvider {
        $repository = new DummyRepository();
        $query = new DummyQuery(DummyRecord::class);
        $query->records = [
            new DummyRecord(),
            new DummyRecord(),
            new DummyRecord(),
            new DummyRecord(),
            new DummyRecord(),
        ];

        return new EntitiesProvider([
            'query' => $query,
            'repository' => $repository,
            'pagination' => false,
            'sort' => false,
        ]);
    }

    protected function createDatProviderWithStubbedArrayData(): EntitiesProvider {
        $repository = new DummyRepository();
        $query = new DummyQuery(DummyRecord::class);
        $query->records = [
            ['id' => 1],
            ['id' => 1],
            ['id' => 1],
            ['id' => 1],
            ['id' => 1],
        ];

        return new EntitiesProvider([
            'query' => $query,
            'repository' => $repository,
            'pagination' => false,
            'sort' => false,
        ]);
    }
}
