<?php

namespace RebelCode\WordPress\Query\FuncTest;

use Dhii\Expression\LogicalExpressionInterface;
use Dhii\Expression\Type\BooleanTypeInterface;
use OutOfRangeException;
use PHPUnit_Framework_MockObject_MockObject;
use Xpmock\TestCase;

/**
 * Tests {@see \RebelCode\WordPress\Query\Builder\BuildWpQueryArgsCapableTrait}.
 *
 * @since [*next-version*]
 */
class BuildWpQueryArgsCapableTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\WordPress\Query\Builder\BuildWpQueryArgsCapableTrait';

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
                                    '_buildWpQueryCompare',
                                    '_buildWpQueryMetaRelation',
                                    '_buildWpQueryTaxRelation',
                                    '_normalizeString',
                                    '_createOutOfRangeException',
                                    '__',
                                ]
                            )
                        );

        $mock = $builder->getMockForTrait();
        $mock->method('__')->willReturnArgument(0);
        $mock->method('_createOutOfRangeException')->willReturnCallback(
            function ($m, $c, $p) {
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
     * Tests the WP Query args build method to assert whether the built result contains the information returned by
     * the abstracted methods.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryArgs()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression(
            BooleanTypeInterface::T_AND,
            [
                $child1 = $this->createLogicalExpression('C1'),
                $child2 = $this->createLogicalExpression('C2'),
                $child3 = $this->createLogicalExpression('C3'),
            ]
        );

        $built1 = uniqid('built-');
        $built2 = uniqid('built-');
        $key2 = uniqid('key-');
        $built3 = uniqid('built-');

        // Child 1 is built as a tax relation
        $subject->method('_buildWpQueryTaxRelation')->willReturnCallback(
            function ($expr) use ($child1, $built1) {
                if ($expr !== $child1) {
                    throw new OutOfRangeException();
                }

                return $built1;
            }
        );

        // Child 2 is built as a top-level comparison
        $subject->method('_buildWpQueryCompare')->willReturnCallback(
            function ($expr) use ($child2, $key2, $built2) {
                if ($expr !== $child2) {
                    throw new OutOfRangeException();
                }

                return [$key2 => $built2];
            }
        );

        // Child 3 is built as a meta relation
        $subject->method('_buildWpQueryMetaRelation')->willReturnCallback(
            function ($expr) use ($child3, $built3) {
                if ($expr !== $child3) {
                    throw new OutOfRangeException();
                }

                return $built3;
            }
        );

        $expected = [
            $key2 => $built2,
            'meta_query' => $built3,
            'tax_query' => $built1,
        ];

        $this->assertEquals(
            $expected,
            $reflect->_buildWpQueryArgs($expression),
            'Expected and retrieved result are not equal'
        );
    }

    /**
     * Tests the WP Query built method with an unsupported expression to assert whether an exception is thrown.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryArgsUnsupported()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression(uniqid('unsupported-'));

        $this->setExpectedException('OutOfRangeException');

        $reflect->_buildWpQueryArgs($expression);
    }

    /**
     * Tests the WP Query built method with a term that is rejected by all build methods to assert whether an exception
     * is thrown.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryFailBuildTerm()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression(
            BooleanTypeInterface::T_AND,
            [
                $child1 = $this->createLogicalExpression('C1'),
                $child2 = $this->createLogicalExpression('C2'),
                $child3 = $this->createLogicalExpression('C3'),
            ]
        );

        // Build function common for all build methods
        // throws an exception if the argument is the second child
        $buildFn = function ($expr) use ($child2) {
            if ($expr == $child2) {
                throw new OutOfRangeException();
            }

            return uniqid('result');
        };
        $subject->method('_buildWpQueryTaxRelation')->willReturnCallback($buildFn);
        $subject->method('_buildWpQueryCompare')->willReturnCallback($buildFn);
        $subject->method('_buildWpQueryMetaRelation')->willReturnCallback($buildFn);

        $this->setExpectedException('OutOfRangeException');

        $reflect->_buildWpQueryArgs($expression);
    }
}
