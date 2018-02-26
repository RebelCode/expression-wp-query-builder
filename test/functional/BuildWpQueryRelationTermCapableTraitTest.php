<?php

namespace RebelCode\WordPress\Query\Builder\FuncTest;

use Dhii\Expression\LogicalExpressionInterface;
use Dhii\Expression\Type\BooleanTypeInterface;
use Dhii\Expression\Type\RelationalTypeInterface;
use OutOfRangeException;
use PHPUnit_Framework_MockObject_MockObject;
use Xpmock\TestCase;

/**
 * Tests {@see \RebelCode\WordPress\Query\Builder\BuildWpQueryRelationTermCapableTrait}.
 *
 * @since [*next-version*]
 */
class BuildWpQueryRelationTermCapableTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\WordPress\Query\Builder\BuildWpQueryRelationTermCapableTrait';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param array $methods Optional additional mock methods.
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function createInstance(array $methods = [])
    {
        $builder = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                        ->setMethods(
                            array_merge(
                                $methods,
                                [
                                    '_buildWpQueryRelation',
                                    '_buildWpQueryMetaCompare',
                                    '_buildWpQueryTaxCompare',
                                    '_createOutOfRangeException',
                                    '_normalizeString',
                                    '__',
                                ]
                            )
                        );

        $mock = $builder->getMockForTrait();
        $mock->method('__')->willReturnArgument(0);
        $mock->method('_createOutOfRangeException')->willReturnCallback(
            function($m = '', $c = 0, $p = null) {
                return new OutOfRangeException($m, $c, $p);
            }
        );
        $mock->method('_normalizeString')->willReturnCallback(
            function($input) {
                return strval($input);
            }
        );

        return $mock;
    }

    /**
     * Creates a mock logical expression instance.
     *
     * @since [*next-version*]
     *
     * @param string $type     The expression type.
     * @param array  $terms    Optional expression terms.
     * @param bool   $negation Optional negation flag. True to negate the expression, false otherwise.
     *
     * @return LogicalExpressionInterface The created mock instance.
     */
    public function createLogicalExpression($type, $terms = [], $negation = false)
    {
        return $this->mock('Dhii\Expression\LogicalExpressionInterface')
                    ->getType($type)
                    ->getTerms($terms)
                    ->isNegated($negation)
                    ->new();
    }

    /**
     * Creates a mock logical expression instance.
     *
     * @since [*next-version*]
     *
     * @param string $type The expression type.
     *
     * @return LogicalExpressionInterface The created mock instance.
     */
    public function createTerm($type)
    {
        return $this->mock('Dhii\Expression\TermInterface')
                    ->getType($type)
                    ->new();
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInternalType(
            'object',
            $subject,
            'An instance of the test subject could not be created'
        );
    }

    /**
     * Tests the build relation term method with a boolean-type term to assert whether the term is attempt to be built
     * as a nested relation.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryRelationTermRelation()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression(
            BooleanTypeInterface::T_AND,
            [
                $term = $this->createLogicalExpression(BooleanTypeInterface::T_AND),
            ]
        );
        $mode = uniqid('mode-');
        $expected = uniqid('result-');

        $subject->expects($this->once())
                ->method('_buildWpQueryRelation')
                ->with($term, $mode)
                ->willReturn($expected);

        $this->assertEquals(
            $expected,
            $reflect->_buildWpQueryRelationTerm($term, $expression, $mode),
            'Retrieved result is not the built relation term result.'
        );
    }

    /**
     * Tests the build relation term method with a meta compare term to assert whether the term is attempt to be built
     * as a meta compare.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryRelationTermMeta()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression(
            BooleanTypeInterface::T_AND,
            [
                $term = $this->createLogicalExpression(RelationalTypeInterface::T_EQUAL_TO),
            ]
        );
        $mode = $reflect->wpQueryRelationModeMeta;
        $expected = uniqid('result-');

        $subject->expects($this->once())
                ->method('_buildWpQueryRelation')
                ->with($term, $mode)
                ->willThrowException(new OutOfRangeException());

        $subject->expects($this->once())
                ->method('_buildWpQueryMetaCompare')
                ->with($term)
                ->willReturn($expected);

        $this->assertEquals(
            $expected,
            $reflect->_buildWpQueryRelationTerm($term, $expression, $mode),
            'Retrieved result is not the built meta compare term result.'
        );
    }

    /**
     * Tests the build relation term method with a tax compare term to assert whether the term is attempt to be built
     * as a tax compare.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryRelationTermTax()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression(
            BooleanTypeInterface::T_AND,
            [
                $term = $this->createLogicalExpression(RelationalTypeInterface::T_EQUAL_TO),
            ]
        );
        $mode = $reflect->wpQueryRelationModeTax;
        $expected = uniqid('result-');

        $subject->expects($this->once())
                ->method('_buildWpQueryRelation')
                ->with($term, $mode)
                ->willThrowException(new OutOfRangeException());

        $subject->expects($this->once())
                ->method('_buildWpQueryTaxCompare')
                ->with($term)
                ->willReturn($expected);

        $this->assertEquals(
            $expected,
            $reflect->_buildWpQueryRelationTerm($term, $expression, $mode),
            'Retrieved result is not the built meta compare term result.'
        );
    }

    /**
     * Tests the build relation term method with a non-logical term to assert whether an exception is thrown.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryRelationInvalidTerm()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression(
            BooleanTypeInterface::T_AND,
            [
                $term = $this->createTerm('literal'),
            ]
        );
        // Use a valid mode to ensure that the exception thrown is not caused by an invalid mode
        $mode = $reflect->wpQueryRelationModeTax;

        $this->setExpectedException('OutOfRangeException');

        $reflect->_buildWpQueryRelationTerm($term, $expression);
    }

    /**
     * Tests the build relation term method with an invalid mode to assert whether an exception is thrown.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryRelationInvalidMode()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression(
            BooleanTypeInterface::T_AND,
            [
                $term = $this->createLogicalExpression(BooleanTypeInterface::T_AND),
            ]
        );
        $mode = uniqid('mode-');

        // Bypass relation mode to force subject to attempt meta/tax compare mode
        $subject->expects($this->once())
                ->method('_buildWpQueryRelation')
                ->with($term, $mode)
                ->willThrowException(new OutOfRangeException());

        $this->setExpectedException('OutOfRangeException');

        $reflect->_buildWpQueryRelationTerm($term, $expression, $mode);
    }
}
