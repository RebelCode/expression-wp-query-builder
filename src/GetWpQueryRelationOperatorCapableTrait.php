<?php

namespace RebelCode\Wordpress\Query\Builder;

use Dhii\Expression\LogicalExpressionInterface;
use Dhii\Expression\Type\BooleanTypeInterface as BoolType;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;
use OutOfRangeException;

/**
 * Common functionality for objects that can retrieve the `WP_Query` relation operator for an expression.
 *
 * @since [*next-version*]
 */
trait GetWpQueryRelationOperatorCapableTrait
{
    /**
     * The map of `WP_Query` relation operators mapped by expression type codes.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $wpQueryRelationOperatorMap = [
        BoolType::T_OR  => 'OR',
        BoolType::T_AND => 'AND',
    ];

    /**
     * Retrieves the WP_Query relation operator from an expression.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression instance to extract from.
     *
     * @throws OutOfRangeException If no relation operator could be determined for the given expression.
     *
     * @return string The relation operator string.
     */
    protected function _getWpQueryRelationOperator(LogicalExpressionInterface $expression)
    {
        $key = $this->_normalizeString($expression->getType());

        if (isset($this->wpQueryRelationOperatorMap[$key])) {
            return $this->wpQueryRelationOperatorMap[$key];
        }

        throw $this->_createOutOfRangeException(
            $this->__('Invalid expression - no relation operator matches the given expression'),
            null,
            null,
            $expression
        );
    }

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
