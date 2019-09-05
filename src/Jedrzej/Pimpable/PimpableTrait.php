<?php namespace Jedrzej\Pimpable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;
use Jedrzej\Searchable\SearchableTrait;
use Jedrzej\Sortable\SortableTrait;
use Jedrzej\Withable\WithableTrait;
use Illuminate\Support\Arr;

trait PimpableTrait
{
    use SearchableTrait;

    use SortableTrait;

    use WithableTrait;

    protected function getSearchableAttributes() {
        return property_exists($this, 'searchable') ? $this->searchable : ['*'];
    }

    protected function getSortableAttributes() {
        return property_exists($this, 'sortable') ? $this->sortable : ['*'];
    }

    protected function getWithableRelations() {
        return property_exists($this, 'withable') ? $this->withable : ['*'];
    }

    /**
     * Enable searchable, sortable and withable scopes
     *
     * @param Builder $builder query builder
     * @param array $query
     */
    public function scopePimp(Builder $builder, $query = [], $sort = [], $relations = [])
    {
        $query = Arr::except($query ?: Request::all(), [$this->sortParameterName, $this->withParameterName]);
        $builder->filtered($query)->sorted($sort)->withRelations($relations);
    }
}
