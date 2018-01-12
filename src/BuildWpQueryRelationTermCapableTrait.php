<?php

namespace RebelCode\Wordpress\Query\Builder;

use Dhii\Expression\LogicalExpressionInterface;
use Dhii\Expression\TermInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Dhii\Util\String\StringableInterface;
use Exception as RootException;
use InvalidArgumentException;

/**
 * Common functionality for objects that can build relation terms.
 *
 * The logic in this implementation attempts to build terms either as nested relations or as meta/tax comparisons.
 *
 * @since [*next-version*]
 */
trait BuildWpQueryRelationTermCapableTrait
{
    /**
     * The relation mode for building terms as meta comparisons.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $wpQueryRelationModeMeta = 'meta';

    /**
     * The relation mode for building terms as meta comparisons.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $wpQueryRelationModeTax = 'tax';

    /**
     * Builds a term into a WP_Query array args portion.
     *
     * @since [*next-version*]
     *
     * @param TermInterface                   $term   The term to build.
     * @param LogicalExpressionInterface      $parent The parent expression of the term.
     * @param string|StringableInterface|null $mode   Optional relation mode to distinguish between meta and tax mode.
     *
     * @throws InvalidArgumentException If the term could not be built.
     *
     * @return array The built term as the sub-array portion that represents it in WP_Query args.
     */
    protected function _buildWpQueryRelationTerm(
        TermInterface $term,
        LogicalExpressionInterface $parent,
        $mode = null
    ) {

        if (!($term instanceof LogicalExpressionInterface)) {
            throw $this->_createInvalidArgumentException(
                $this->__('Relation term is not a not a logical expression'),
                null,
                null,
                $term
            );
        }

        try {
            // Will throw if not a WP_Query relation expression
            return $this->_buildWpQueryRelation($term, $mode);
        } catch (InvalidArgumentException $iArgEx) {
            return $this->_buildWpQueryMetaTaxCompare($term, $mode);
        }
    }

    /**
     * Attempts to build a relation term as a meta or tax comparison.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $term The term to build.
     * @param null                       $mode Optional relation mode to distinguish between meta and tax mode.
     *
     * @return array The built term as the meta or tax comparison sub-array potion.
     *
     * @throws InvalidArgumentException If the relation mode arg is invalid.
     */
    protected function _buildWpQueryMetaTaxCompare(LogicalExpressionInterface $term, $mode = null)
    {
        $mode = $this->_normalizeString($mode);

        if ($mode === $this->wpQueryRelationModeMeta) {
            return $this->_buildWpQueryMetaCompare($term);
        }

        if ($mode === $this->wpQueryRelationModeTax) {
            return $this->_buildWpQueryTaxCompare($term);
        }

        throw $this->_createInvalidArgumentException(
            $this->__('Invalid relation mode - cannot build relation term'),
            null,
            null,
            $term
        );
    }

    /**
     * Builds a given logical expression into a WP_Query relation array args portion.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression to build.
     * @param string|Stringable|null     $mode       Optional relation mode, either "meta" or "tax".
     *
     * @return array The built expression, as the sub-array portion that represents it in WP_Query args.
     */
    abstract protected function _buildWpQueryRelation(LogicalExpressionInterface $expression, $mode = null);

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
    abstract protected function _buildWpQueryMetaCompare(LogicalExpressionInterface $expression);

    /**
     * Builds a given logical expression into a WP_Query taxonomy compare array args portion.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression to build.
     *
     * @return array The built taxonomy compare sub-array portion that represents it in WP_Query args.
     */
    abstract protected function _buildWpQueryTaxCompare(LogicalExpressionInterface $expression);

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
