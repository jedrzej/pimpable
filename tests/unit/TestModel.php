<?php

use Illuminate\Database\Eloquent\Model;
use Jedrzej\Pimpable\PimpableTrait;

class TestModel extends Model
{
    use PimpableTrait;

    protected function newBaseQueryBuilder()
    {
        return new TestBuilder;
    }
}