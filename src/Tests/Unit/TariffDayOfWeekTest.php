<?php

namespace Ocpi\Tests\Unit;

use Illuminate\Support\Collection;
use Mockery;
use Mockery\MockInterface;
use Ocpi\Models\Tariffs\TariffRestriction as TariffRestrictionModel;
use Ocpi\Modules\Tariffs\Enums\DayOfWeek;
use Ocpi\Modules\Tariffs\Factories\TariffRestrictionFactory;
use Ocpi\Modules\Tariffs\Objects\TariffRestriction as TariffRestrictionObject;
use Ocpi\Tests\TestCase;

class TariffDayOfWeekTest extends TestCase
{
    public function testEnumExposesSevenOcpiSpecCases(): void
    {
        $expected = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'];
        $actual = array_map(fn (DayOfWeek $d) => $d->value, DayOfWeek::cases());

        $this->assertSame($expected, $actual);
    }

    public function testEnumFromAcceptsValidValueAndRejectsInvalid(): void
    {
        $this->assertSame(DayOfWeek::MONDAY, DayOfWeek::from('MONDAY'));
        $this->assertNull(DayOfWeek::tryFrom('monday'));
        $this->assertNull(DayOfWeek::tryFrom('FUNDAY'));
    }

    public function testDtoRoundTripsDayOfWeek(): void
    {
        $days = [DayOfWeek::MONDAY, DayOfWeek::WEDNESDAY, DayOfWeek::FRIDAY];

        $restriction = (new TariffRestrictionObject(1))->setDayOfWeek($days);

        $this->assertSame($days, $restriction->getDayOfWeek());
    }

    public function testDtoToArraySerializesDayOfWeekToStringValues(): void
    {
        $restriction = (new TariffRestrictionObject(1))
            ->setDayOfWeek([DayOfWeek::SATURDAY, DayOfWeek::SUNDAY]);

        $this->assertSame(
            ['SATURDAY', 'SUNDAY'],
            $restriction->toArray()['day_of_week'],
        );
    }

    public function testFactoryConvertsModelCollectionToEnumArray(): void
    {
        $modelMock = $this->mockTariffRestrictionModel(
            new Collection([DayOfWeek::MONDAY, DayOfWeek::TUESDAY]),
        );

        $object = TariffRestrictionFactory::fromModel($modelMock);

        $this->assertSame(
            [DayOfWeek::MONDAY, DayOfWeek::TUESDAY],
            $object->getDayOfWeek(),
        );
    }

    public function testFactoryReturnsEmptyArrayWhenDayOfWeekIsNull(): void
    {
        $modelMock = $this->mockTariffRestrictionModel(null);

        $object = TariffRestrictionFactory::fromModel($modelMock);

        $this->assertSame([], $object->getDayOfWeek());
    }

    private function mockTariffRestrictionModel(?Collection $dayOfWeek): TariffRestrictionModel
    {
        return Mockery::mock(
            TariffRestrictionModel::class,
            function (MockInterface $mock) use ($dayOfWeek) {
                $mock->shouldReceive('setAttribute')->andReturnSelf();
                $mock->shouldReceive('getAttribute')->with('id')->andReturn(42);
                $mock->shouldReceive('getAttribute')->with('start_time')->andReturnNull();
                $mock->shouldReceive('getAttribute')->with('end_time')->andReturnNull();
                $mock->shouldReceive('getAttribute')->with('start_date')->andReturnNull();
                $mock->shouldReceive('getAttribute')->with('end_date')->andReturnNull();
                $mock->shouldReceive('getAttribute')->with('min_kwh')->andReturnNull();
                $mock->shouldReceive('getAttribute')->with('max_kwh')->andReturnNull();
                $mock->shouldReceive('getAttribute')->with('min_current')->andReturnNull();
                $mock->shouldReceive('getAttribute')->with('max_current')->andReturnNull();
                $mock->shouldReceive('getAttribute')->with('min_power')->andReturnNull();
                $mock->shouldReceive('getAttribute')->with('max_power')->andReturnNull();
                $mock->shouldReceive('getAttribute')->with('min_duration')->andReturnNull();
                $mock->shouldReceive('getAttribute')->with('max_duration')->andReturnNull();
                $mock->shouldReceive('getAttribute')->with('day_of_week')->andReturn($dayOfWeek);
            },
        );
    }
}