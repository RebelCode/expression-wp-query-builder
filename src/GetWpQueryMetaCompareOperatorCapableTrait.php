<?php

namespace RebelCode\Wordpress\Query\Builder;

use Dhii\Expression\LogicalExpressionInterface;
use Dhii\Expression\Type\RelationalTypeInterface as RelType;
use Dhii\Storage\Resource\Sql\Expression\SqlRelationalTypeInterface as SqlRelType;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;

/**
 * Common functionality for objects that can retrieve the `WP_Query` meta compare operator for an expression.
 *
 * @since [*next-version*]
 */
trait GetWpQueryMetaCompareOperatorCapableTrait
{
    /**
     * The map of `WP_Query` meta comparison operators mapped by expression type codes.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $wpQueryMetaCompareOperatorMap = [
        RelType::T_EQUAL_TO         => ['=', '!='],
        RelType::T_GREATER_THAN     => ['>', '<='],
        RelType::T_GREATER_EQUAL_TO => ['>=', '<'],
        RelType::T_LESS_THAN        => ['<', '>='],
        RelType::T_LESS_EQUAL_TO    => ['<=', '>'],
        SqlRelType::T_LIKE          => ['LIKE', 'NOT LIKE'],
        SqlRelType::T_IN            => ['IN', 'NOT IN'],
        SqlRelType::T_BETWEEN       => ['BETWEEN', 'NOT BETWEEN'],
        SqlRelType::T_EXISTS        => ['EXISTS', 'NOT EXISTS'],
    ];

    /**
     * Retrieves the WP_Query meta compare operator from an expression.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression instance to extract from.
     *
     * @throws InvalidArgumentException If the meta compare operator cannot be determined.
     *
     * @return string The compare operator string.
     */
    protected function _getWpQueryMetaCompareOperator(LogicalExpressionInterface $expression)
    {
        $key = $this->_normalizeString($expression->getType());
        $neg = ((int) $expression->isNegated()) % 2;

        if (isset($this->wpQueryMetaCompareOperatorMap[$key])) {
            return $this->wpQueryMetaCompareOperatorMap[$key][$neg];
        }

        throw $this->_createInvalidArgumentException(
            $this->__('Invalid expression - no meta compare operator matches the given expression'),
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
