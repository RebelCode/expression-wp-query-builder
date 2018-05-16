<?php

namespace RebelCode\WordPress\Query\Builder;

use Dhii\Expression\LogicalExpressionInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use OutOfRangeException;

/**
 * Common functionality for objects that can build `WP_Query` taxonomy compare expression args.
 *
 * Taxonomy comparisons can be found in the `tax_query` portion of the array argument for `WP_Query`, and represent a
 * single taxonomy term comparison. Example:
 *
 * ```php
 * <?php
 * [
 *     'taxonomy' => 'category',
 *     'field'    => 'slug',
 *     'terms'    => 'sports'
 * ]
 * ```
 *
 * @since [*next-version*]
 */
trait BuildWpQueryTaxCompareCapableTrait
{
    /**
     * Builds a given logical expression into a WP_Query taxonomy compare array args portion.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression to build.
     *
     * @return array The built taxonomy compare sub-array portion that represents it in WP_Query args.
     *
     * @throws OutOfRangeException If the given expression is not supported as a `WP_Query` taxonomy compare.
     */
    protected function _buildWpQueryTaxCompare(LogicalExpressionInterface $expression)
    {
        try {
            $result = [
                'taxonomy' => $this->_getWpQueryTaxCompareTaxonomy($expression),
                'field'    => $this->_getWpQueryTaxCompareField($expression),
                'terms'    => $this->_getWpQueryTaxCompareTerms($expression),
                'operator' => $this->_getWpQueryTaxCompareOperator($expression),
            ];

            return $result;
        } catch (RootException $exception) {
            throw $this->_createOutOfRangeException(
                $this->__('Expression could not be built into a WP_Query taxonomy compare'),
                null,
                $exception,
                $expression
            );
        }
    }

    /**
     * Retrieves the WP_Query taxonomy compare name from an expression.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression instance to extract from.
     *
     * @throws OutOfRangeException If the taxonomy compare name cannot be determined.
     *
     * @return string The taxonomy name string.
     */
    abstract protected function _getWpQueryTaxCompareTaxonomy(LogicalExpressionInterface $expression);

    /**
     * Retrieves the WP_Query taxonomy compare field from an expression.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression instance to extract from.
     *
     * @throws OutOfRangeException If the taxonomy compare field cannot be determined.
     *
     * @return string The taxonomy field string.
     */
    abstract protected function _getWpQueryTaxCompareField(LogicalExpressionInterface $expression);

    /**
     * Retrieves the WP_Query taxonomy compare terms from an expression.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression instance to extract from.
     *
     * @throws OutOfRangeException If the taxonomy compare field cannot be determined.
     *
     * @return array A list of taxonomy term strings.
     */
    abstract protected function _getWpQueryTaxCompareTerms(LogicalExpressionInterface $expression);

    /**
     * Retrieves the WP_Query taxonomy compare operator from an expression.
     *
     * If an operator could not be determined, 'IN' should be returned as a default.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression instance to extract from.
     *
     * @return string The taxonomy compare operator string.
     */
    abstract protected function _getWpQueryTaxCompareOperator(LogicalExpressionInterface $expression);

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
