<?php

use Codeception\Specify;
use Codeception\TestCase\Test;
use Jedrzej\Searchable\Constraint;

class PimpableTraitTest extends Test
{
    use Specify;

    public function testFiltering()
    {
        $this->specify("constraints are applied when query is given", function () {
            $this->assertCount(1, (array)TestModel::pimp(['field1' => 5], ['id,asc'], 'owner')->getQuery()->wheres);
            $this->assertCount(2, (array)TestModel::pimp(['field1' => 5, 'field2' => 3], ['id,asc'], 'owner')->getQuery()->wheres);
        });

        $this->specify("constraints are applied to columns by name", function () {
            $where = TestModel::pimp(['field1' => '!abc,cde'], ['id,asc'], 'owner')->getQuery()->wheres[0];
            $this->assertEquals('field1', $where['column']);
        });

        $this->specify("constraints are applied correctly", function () {
            $where = TestModel::pimp(['field1' => 'abc'], ['id,asc'], 'owner', ['id,asc'], 'owner')->getQuery()->wheres[0];
            $this->assertEquals('Basic', $where['type']);
            $this->assertEquals('abc', $where['value']);
            $this->assertEquals('=', $where['operator']);
            $this->assertEquals('field1', $where['column']);
            $where = TestModel::pimp(['field1' => 'abc,cde'], ['id,asc'], 'owner')->getQuery()->wheres[0];
            $this->assertEquals('In', $where['type']);
            $this->assertEquals(['abc', 'cde'], $where['values']);
            $this->assertEquals('field1', $where['column']);
            $where = TestModel::pimp(['field1' => '(gt)5'], ['id,asc'], 'owner')->getQuery()->wheres[0];
            $this->assertEquals('Basic', $where['type']);
            $this->assertEquals('5', $where['value']);
            $this->assertEquals('>', $where['operator']);
            $this->assertEquals('field1', $where['column']);
            $where = TestModel::pimp(['field1' => '(ge)5'], ['id,asc'], 'owner')->getQuery()->wheres[0];
            $this->assertEquals('Basic', $where['type']);
            $this->assertEquals('5', $where['value']);
            $this->assertEquals('>=', $where['operator']);
            $this->assertEquals('field1', $where['column']);
            $where = TestModel::pimp(['field1' => '(lt)5'], ['id,asc'], 'owner')->getQuery()->wheres[0];
            $this->assertEquals('Basic', $where['type']);
            $this->assertEquals('5', $where['value']);
            $this->assertEquals('<', $where['operator']);
            $this->assertEquals('field1', $where['column']);
            $where = TestModel::pimp(['field1' => '(le)5'], ['id,asc'], 'owner')->getQuery()->wheres[0];
            $this->assertEquals('Basic', $where['type']);
            $this->assertEquals('5', $where['value']);
            $this->assertEquals('<=', $where['operator']);
            $this->assertEquals('field1', $where['column']);
            $where = TestModel::pimp(['field1' => 'abc%'], ['id,asc'], 'owner')->getQuery()->wheres[0];
            $this->assertEquals('Basic', $where['type']);
            $this->assertEquals('abc%', $where['value']);
            $this->assertEquals('like', $where['operator']);
            $this->assertEquals('field1', $where['column']);
            $where = TestModel::pimp(['field1' => '!abc'], ['id,asc'], 'owner')->getQuery()->wheres[0];
            $this->assertEquals('Basic', $where['type']);
            $this->assertEquals('abc', $where['value']);
            $this->assertEquals('<>', $where['operator']);
            $this->assertEquals('field1', $where['column']);
            $where = TestModel::pimp(['field1' => '!abc,cde'], ['id,asc'], 'owner')->getQuery()->wheres[0];
            $this->assertEquals('NotIn', $where['type']);
            $this->assertEquals(['abc', 'cde'], $where['values']);
            $this->assertEquals('field1', $where['column']);
            $where = TestModel::pimp(['field1' => '!(gt)5'], ['id,asc'], 'owner')->getQuery()->wheres[0];
            $this->assertEquals('Basic', $where['type']);
            $this->assertEquals('5', $where['value']);
            $this->assertEquals('<=', $where['operator']);
            $this->assertEquals('field1', $where['column']);
            $where = TestModel::pimp(['field1' => '!(ge)5'], ['id,asc'], 'owner')->getQuery()->wheres[0];
            $this->assertEquals('Basic', $where['type']);
            $this->assertEquals('5', $where['value']);
            $this->assertEquals('<', $where['operator']);
            $this->assertEquals('field1', $where['column']);
            $where = TestModel::pimp(['field1' => '!(lt)5'], ['id,asc'], 'owner')->getQuery()->wheres[0];
            $this->assertEquals('Basic', $where['type']);
            $this->assertEquals('5', $where['value']);
            $this->assertEquals('>=', $where['operator']);
            $this->assertEquals('field1', $where['column']);
            $where = TestModel::pimp(['field1' => '!(le)5'], ['id,asc'], 'owner')->getQuery()->wheres[0];
            $this->assertEquals('Basic', $where['type']);
            $this->assertEquals('5', $where['value']);
            $this->assertEquals('>', $where['operator']);
            $this->assertEquals('field1', $where['column']);
            $where = TestModel::pimp(['field1' => '!abc%'], ['id,asc'], 'owner')->getQuery()->wheres[0];
            $this->assertEquals('Basic', $where['type']);
            $this->assertEquals('abc%', $where['value']);
            $this->assertEquals('not like', $where['operator']);
            $this->assertEquals('field1', $where['column']);
        });

        $this->specify("multiple constraints can be given for a single attribute", function () {
            $wheres = (array)TestModel::pimp(['field1' => ['(gt)3', '(lt)10']], ['id,asc'], 'owner')->getQuery()->wheres;
            $this->assertCount(2, $wheres);
            $this->assertEquals('Basic', $wheres[0]['type']);
            $this->assertEquals('3', $wheres[0]['value']);
            $this->assertEquals('>', $wheres[0]['operator']);
            $this->assertEquals('field1', $wheres[0]['column']);
            $this->assertEquals('Basic', $wheres[1]['type']);
            $this->assertEquals('10', $wheres[1]['value']);
            $this->assertEquals('<', $wheres[1]['operator']);
            $this->assertEquals('field1', $wheres[0]['column']);
            $wheres = (array)TestModel::pimp(['field1' => ['100%', '!10']], ['id,asc'], 'owner')->getQuery()->wheres;
            $this->assertCount(2, $wheres);
            $this->assertEquals('Basic', $wheres[0]['type']);
            $this->assertEquals('100%', $wheres[0]['value']);
            $this->assertEquals('like', $wheres[0]['operator']);
            $this->assertEquals('field1', $wheres[0]['column']);
            $this->assertEquals('Basic', $wheres[1]['type']);
            $this->assertEquals('10', $wheres[1]['value']);
            $this->assertEquals('<>', $wheres[1]['operator']);
            $this->assertEquals('field1', $wheres[1]['column']);
            $wheres = (array)TestModel::pimp(['field1' => ['20%', '!2013%']], ['id,asc'], 'owner')->getQuery()->wheres;
            $this->assertCount(2, $wheres);
            $this->assertEquals('Basic', $wheres[0]['type']);
            $this->assertEquals('20%', $wheres[0]['value']);
            $this->assertEquals('like', $wheres[0]['operator']);
            $this->assertEquals('field1', $wheres[0]['column']);
            $this->assertEquals('Basic', $wheres[1]['type']);
            $this->assertEquals('2013%', $wheres[1]['value']);
            $this->assertEquals('not like', $wheres[1]['operator']);
            $this->assertEquals('field1', $wheres[1]['column']);
        });

        $this->specify("mode is recognized and applied correctly", function() {
            $this->assertCount(2, (array)TestModel::pimp(['field1' => 5, 'field2' => 3, 'mode' => 'and'], ['id,asc'], 'owner')->getQuery()->wheres);
            foreach (TestModel::pimp(['field1' => 5, 'field2' => 3, 'mode' => 'and'], ['id,asc'], 'owner')->getQuery()->wheres as $where) {
                $this->assertEquals(Constraint::MODE_AND, $where['boolean']);
            }
            $this->assertCount(2, (array)TestModel::pimp(['field1' => 5, 'field2' => 3, 'mode' => 'or'], ['id,asc'], 'owner')->getQuery()->wheres);
            foreach (TestModel::pimp(['field1' => 5, 'field2' => 3, 'mode' => 'or'], ['id,asc'], 'owner')->getQuery()->wheres as $where) {
                $this->assertEquals(Constraint::MODE_OR, $where['boolean']);
            }
        });

        $this->specify("AND mode is the default value if no mode or invalid mode is provided", function() {
            $this->assertCount(2, (array)TestModel::pimp(['field1' => 5, 'field2' => 3, 'mode' => 'invalid'], ['id,asc'], 'owner')->getQuery()->wheres);
            foreach (TestModel::pimp(['field1' => 5, 'field2' => 3, 'mode' => 'invalid'], ['id,asc'], 'owner')->getQuery()->wheres as $where) {
                $this->assertEquals(Constraint::MODE_AND, $where['boolean']);
            }
            $this->assertCount(2, (array)TestModel::pimp(['field1' => 5, 'field2' => 3], ['id,asc'], 'owner')->getQuery()->wheres);
            foreach (TestModel::pimp(['field1' => 5, 'field2' => 3], ['id,asc'], 'owner')->getQuery()->wheres as $where) {
                $this->assertEquals(Constraint::MODE_AND, $where['boolean']);
            }
        });

        $this->specify("query paramters relevant to related sortable/withable bundles are excluded", function () {
            $wheres = (array)TestModel::pimp(['sort' => 'sort', 'with' => 'with', 'field1' => 'field1'], ['id,asc'], 'owner')->getQuery()->wheres;
            $this->assertCount(1, $wheres);
            $this->assertEquals('Basic', $wheres[0]['type']);
            $this->assertEquals('field1', $wheres[0]['value']);
            $this->assertEquals('=', $wheres[0]['operator']);
            $this->assertEquals('field1', $wheres[0]['column']);
            $wheres = (array)TestModelWithAlteredSortParameterName::pimp(['sort' => 'sort', 'with' => 'with', 'field1' => 'field1'], ['id,asc'], 'owner')->getQuery()->wheres;
            $this->assertCount(2, $wheres);
            $this->assertEquals('Basic', $wheres[0]['type']);
            $this->assertEquals('sort', $wheres[0]['value']);
            $this->assertEquals('=', $wheres[0]['operator']);
            $this->assertEquals('sort', $wheres[0]['column']);
            $this->assertEquals('Basic', $wheres[1]['type']);
            $this->assertEquals('field1', $wheres[1]['value']);
            $this->assertEquals('=', $wheres[1]['operator']);
            $this->assertEquals('field1', $wheres[1]['column']);
            $wheres = (array)TestModelWithAlteredSortParameterName::pimp(['orderBy' => 'orderBy', 'with' => 'with', 'field1' => 'field1'], ['id,asc'], 'owner')->getQuery()->wheres;
            $this->assertCount(1, $wheres);
            $this->assertEquals('Basic', $wheres[0]['type']);
            $this->assertEquals('field1', $wheres[0]['value']);
            $this->assertEquals('=', $wheres[0]['operator']);
            $this->assertEquals('field1', $wheres[0]['column']);
        });
    }

    public function testSorting() {
        $this->specify("sort criterion is applied when only one is given", function () {
            $this->assertCount(1, (array)TestModel::pimp(['a'=>'b'], ['sort' => 'field1,asc'], 'owner')->getQuery()->orders);
        });

        $this->specify("sort criteria are applied when array is given", function () {
            $this->assertCount(2, (array)TestModel::pimp(['a'=>'b'],  ['sort' => ['field1,asc', 'field2,desc']], 'owner')->getQuery()->orders);
        });

        $this->specify("criteria are applied to columns by name", function () {
            $criterion = (array)TestModel::pimp(['a'=>'b'],  ['sort' => 'field1,asc'], 'owner')->getQuery()->orders[0];
            $this->assertEquals('field1', $criterion['column']);
        });

        $this->specify("criteria are applied in the same order as specified", function () {
            $criteria = (array)TestModel::pimp(['a'=>'b'], ['sort' => ['field1,desc', 'field2,desc']], 'owner')->getQuery()->orders;
            $this->assertEquals('field1', $criteria[0]['column']);
            $this->assertEquals('field2', $criteria[1]['column']);

            $criteria = (array)TestModel::pimp(['a'=>'b'], ['sort' => ['field2,desc', 'field1,desc']], 'owner')->getQuery()->orders;
            $this->assertEquals('field2', $criteria[0]['column']);
            $this->assertEquals('field1', $criteria[1]['column']);
        });
    }

    public function testLoadingRelations() {
        $this->specify("eager loading is applied when only one is given", function () {
            $this->assertCount(1, TestModel::pimp(['a' => 'b'], ['sort' => 'field1,asc'],'relation1')->getEagerLoads());
            $this->assertContains('relation1', array_keys(TestModel::pimp(['a' => 'b'], ['sort' => 'field1,asc'],'relation1')->getEagerLoads()));
            $this->assertNotContains('relation2', array_keys(TestModel::pimp(['a' => 'b'], ['sort' => 'field1,asc'],'relation1')->getEagerLoads()));
        });

        $this->specify("eager loading are applied when array is given", function () {
            $this->assertCount(2, TestModel::pimp(['a' => 'b'], ['sort' => 'field1,asc'],['relation1', 'relation2'])->getEagerLoads());
            $this->assertContains('relation1', array_keys(TestModel::pimp(['a' => 'b'], ['sort' => 'field1,asc'],['relation1', 'relation2'])->getEagerLoads()));
            $this->assertContains('relation2', array_keys(TestModel::pimp(['a' => 'b'], ['sort' => 'field1,asc'],['relation1', 'relation2'])->getEagerLoads()));
            $this->assertNotContains('relation3', array_keys(TestModel::pimp(['a' => 'b'], ['sort' => 'field1,asc'],['relation1', 'relation2'])->getEagerLoads()));
        });
    }
}
