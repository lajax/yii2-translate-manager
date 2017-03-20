<?php

namespace lajax\translatemanager\models\searches;

/**
 * Model search helper trait. Helps with common search tasks.
 *
 * It must be used in a \yii\db\ActiveRecord context.
 *
 * @since 1.5.2
 */
trait SearchTrait
{
    /**
     * Creates a `LIKE` expression, which can be used in the Query builder.
     *
     * @param mixed $operand1 Should be a column or DB expression.
     * @param mixed $operand2 Should be a string or an array representing the values that the column or DB expression
     * should be like.
     *
     * @return array
     *
     * @see http://www.yiiframework.com/doc-2.0/yii-db-query.html#where%28%29-detail
     */
    protected function createLikeExpression($operand1, $operand2)
    {
        /* @var $db \yii\db\Connection */
        $db = static::getDb();

        // In PGSQL we use case insensitive matching also.
        $like_function = $db->getDriverName() == 'pgsql' ? 'ILIKE' : 'LIKE';

        return [$like_function, $operand1, $operand2];
    }
}
