<?php

namespace RebelCode\WordPress\Query\Builder;

use Dhii\Expression\LogicalExpressionInterface;
use Dhii\Expression\Type\RelationalTypeInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;
use OutOfRangeException;

/**
 * Common functionality for objects that can build comparison entries in `WP_Query` argument arrays.
 *
 * Comparison entries in `WP_Query` args are the key-value pairs found at the top-most level of the argument array.
 * For example, the below are three comparison entries:
 *
 * ```php
 * <?php
 * [
 *     'post_type'   => 'page',
 *     'post_status' => 'draft',
 *     'author__in'  => [12, 18, 19]
 * ]
 * ```
 *
 * @since [*next-version*]
 */
trait BuildWpQueryCompareCapableTrait
{
    /**
     * Builds a given logical expression into a WP_Query compare array args portion.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression to build.
     *
     * @return array The built expression, as the sub-array portion that represents it in WP_Query args.
     *
     * @throws OutOfRangeException If the given expression is not supported.
     */
    protected function _buildWpQueryCompare(LogicalExpressionInterface $expression)
    {
        if (!$this->_isWpQueryCompareExpressionSupported($expression)) {
            throw $this->_createOutOfRangeException(
                $this->__('Expression type is not supported'),
                null,
                null,
                $expression
            );
        }

        $key = $this->_getWpQueryCompareKey($expression);
        $value = $this->_getWpQueryCompareValue($expression);

        return [$key => $value];
    }

    /**
     * Checks whether a given expression is supported as a WP_Query compare expression.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression instance to check.
     *
     * @return bool True if the expression is supported as a WP_Query compare expression, false if not.
     */
    protected function _isWpQueryCompareExpressionSupported(LogicalExpressionInterface $expression)
    {
        return $this->_normalizeString($expression->getType()) === RelationalTypeInterface::T_EQUAL_TO;
    }

    /**
     * Retrieves the WP_Query compare key from an expression.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression instance to extract from.
     *
     * @throws OutOfRangeException If the compare key could not be determined/retrieved.
     *
     * @return string The compare key string.
     */
    abstract protected function _getWpQueryCompareKey(LogicalExpressionInterface $expression);

    /**
     * Retrieves the WP_Query compare value from an expression.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression instance to extract from.
     *
     * @throws OutOfRangeException If the compare value could not be determined/retrieved.
     *
     * @return mixed The compare value.
     */
    abstract protected function _getWpQueryCompareValue(LogicalExpressionInterface $expression);

    /**
     * Normalizes a value to its string representation.
     *
     * The values that can be normalized are any scalar values, as well as
     * {@see StringableInterface).
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $subject The value to normalize to string.
     *
     * @throws InvalidArgumentException If the value cannot be normalized.
     *
     * @return string The string that resulted from normalization.
     */
    abstract protected function _normalizeString($subject);

    /**
     * Creates a new Out Of Range exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param mixed|null             $argument The invalid argument, if any.
     *
     * @return OutOfRangeException The new exception.
     */
    abstract protected function _createOutOfRangeException(
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
