<?php

namespace RebelCode\Wordpress\Query\Builder;

use Dhii\Expression\LogicalExpressionInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;

/**
 * Common functionality for objects that can build expressions into `WP_Query` meta comparisons.
 *
 * The meta comparisons in a `WP_Query` argument array are the sub-array portions that represent a meta key comparison
 * with a value. They are found as entries in a relational array and take the following form:
 *
 * ```php
 * <?php
 * [
 *     'key'     => 'my_meta_key',
 *     'value'   => 100,
 *     'type'    => 'NUMERIC',
 *     'compare' => '<='
 * ]
 * ```
 *
 * @since [*next-version*]
 */
trait BuildWpQueryMetaCompareCapableTrait
{
    /**
     * Builds a given logical expression into a WP_Query meta compare array args portion.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression to build.
     *
     * @throws InvalidArgumentException If the given expression could not be built into a WP_Query meta compare.
     *
     * @return array The built meta compare sub-array portion that represents it in WP_Query args.
     */
    protected function _buildWpQueryMetaCompare(LogicalExpressionInterface $expression)
    {
        try {
            $result = [
                'key'     => $this->_getWpQueryMetaCompareKey($expression),
                'value'   => $this->_getWpQueryMetaCompareValue($expression),
                'type'    => $this->_getWpQueryMetaCompareType($expression),
                'compare' => $this->_getWpQueryMetaCompareOperator($expression),
            ];

            return $result;
        } catch (RootException $exception) {
            throw $this->_createInvalidArgumentException(
                $this->__('Expression could not be built into a WP_Query meta compare'),
                null,
                $exception,
                $expression
            );
        }
    }

    /**
     * Retrieves the WP_Query compare key from an expression.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression instance to extract from.
     *
     * @throws InvalidArgumentException If the meta compare key cannot be determined.
     *
     * @return string The compare key string.
     */
    abstract protected function _getWpQueryMetaCompareKey(LogicalExpressionInterface $expression);

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
    abstract protected function _getWpQueryMetaCompareType(LogicalExpressionInterface $expression);

    /**
     * Retrieves the WP_Query compare operator from an expression.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression instance to extract from.
     *
     * @throws InvalidArgumentException If the meta compare operator cannot be determined.
     *
     * @return string The compare operator string.
     */
    abstract protected function _getWpQueryMetaCompareOperator(LogicalExpressionInterface $expression);

    /**
     * Creates a new invalid argument exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param mixed|null             $argument The invalid argument, if any.
     *
     * @return InvalidArgumentException The new exception.
     */
    abstract protected function _createInvalidArgumentException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $argument = null
    );

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see   sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
