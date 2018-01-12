<?php

namespace RebelCode\Wordpress\Query\Builder;

use Dhii\Expression\LogicalExpressionInterface;
use InvalidArgumentException;

/**
 * Common functionality for objects that can provide the compare type for `WP_Query` meta comparisons.
 *
 * This implementation uses the meta compare value's internal type to determine whether a numeric or boolean type can
 * be used. If not, the default 'CHAR' type is used.
 *
 * @since [*next-version*]
 */
trait GetWpQueryMetaCompareTypeCapableTrait
{
    /**
     * Retrieves the WP_Query compare type from an expression.
     *
     * If no type could be determined, 'CHAR' should be returned as a default.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression instance to extract from.
     *
     * @return string The compare type string.
     */
    protected function _getWpQueryMetaCompareType(LogicalExpressionInterface $expression)
    {
        try {
            $value = $this->_getWpQueryMetaCompareValue($expression);
            $type = gettype($value);

            if ($type === 'integer') {
                return 'NUMERIC';
            }

            if ($type === 'double') {
                return 'DECIMAL';
            }

            if ($type === 'boolean') {
                return 'BINARY';
            }
        } catch (InvalidArgumentException $ex) {
        }

        return 'CHAR';
    }

    /**
     * Retrieves the WP_Query compare value from an expression.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression instance to extract from.
     *
     * @throws InvalidArgumentException If the meta compare value cannot be determined.
     *
     * @return mixed The compare value.
     */
    abstract protected function _getWpQueryMetaCompareValue(LogicalExpressionInterface $expression);
}
