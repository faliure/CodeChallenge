<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RankItem extends TestCase
{

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRankItemCreationWithNonObjectInput()
    {
        \App\RankItem::create([
            'user_id'    => 1,
            'user_name'  => 'name',
            'country_id' => 2,
            'position'   => 3,
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRankItemCreationWithMissingUserIdThrowsException()
    {
        \App\RankItem::create((object) [
            'user_name'  => 'name',
            'country_id' => 2,
            'position'   => 3,
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRankItemCreationWithMissingUserNameThrowsException()
    {
        \App\RankItem::create((object) [
            'user_id'    => 1,
            'country_id' => 2,
            'position'   => 3,
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRankItemCreationWithMissingCountryIdThrowsException()
    {
        \App\RankItem::create((object) [
            'user_id'    => 1,
            'user_name'  => 'name',
            'position'   => 3,
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRankItemCreationWithMissingPositionThrowsException()
    {
        \App\RankItem::create((object) [
            'user_id'    => 1,
            'user_name'  => 'name',
            'country_id' => 2,
        ]);
    }

    /**
     * @covers \App\RankItem::__get()
     */
    public function testRankItemMagicGetter()
    {
        $data = [
            'user_id'    => 1,
            'user_name'  => 'name',
            'country_id' => 2,
            'position'   => 3,
        ];

        $item = \App\RankItem::create((object) $data);

        foreach ($data as $key => $val) {
            $this->assertEquals($val, $item->$key);
        }
    }

    /**
     * @covers \App\RankItem::__set()
     */
    public function testRankItemMagicSetter()
    {
        $item = \App\RankItem::create((object) [
            'user_id'    => 1,
            'user_name'  => 'name',
            'country_id' => 2,
            'position'   => 3,
        ]);

        $item->position = 18;
        $this->assertEquals(18, $item->position);

        $item->newField = 'newField';
        $this->assertEquals('newField', $item->newField);
    }
}
