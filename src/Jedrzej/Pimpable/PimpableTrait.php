<?php namespace Jedrzej\Pimpable;

use Illuminate\Database\Eloquent\Builder;
use Jedrzej\Searchable\SearchableTrait;
use Jedrzej\Sortable\SortableTrait;
use Jedrzej\Withable\WithableTrait;

trait PimpableTrait
{
    use SearchableTrait;

    use SortableTrait;

    use WithableTrait;

    protected $searchable = ['*'];

    protected $sortable = ['*'];

    protected $withable = ['*'];

    protected $parameterSort = 'sort';

    protected $parameterWith = 'with';

    /**
     * Enable searchable, sortable and withable scopes
     *
     * @param Builder $builder query builder
     * @param array $query
     */
    public function scopePimp(Builder $builder, $query = [], $sort = [], $relations = [])
    {
        $query = $query ?: array_except([$this->parameterSort, $this->parameterWith]);
        $builder->filtered($query)->sorted($sort)->withRelations($relations);
    }
}
