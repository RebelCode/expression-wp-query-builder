<?php

namespace RebelCode\WordPress\Query\Builder\FuncTest;

use Dhii\Expression\LogicalExpressionInterface;
use Dhii\Expression\Type\BooleanTypeInterface;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use Xpmock\TestCase;

/**
 * Tests {@see \RebelCode\WordPress\Query\Builder\GetWpQueryRelationOperatorCapableTrait}.
 *
 * @since [*next-version*]
 */
class GetWpQueryRelationOperatorCapableTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\WordPress\Query\Builder\GetWpQueryRelationOperatorCapableTrait';

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
                                    '__',
                                    '_createInvalidArgumentException',
                                    '_normalizeString',
                                ]
                            )
                        );

        $mock = $builder->getMockForTrait();
        $mock->method('__')->willReturnArgument(0);
        $mock->method('_createInvalidArgumentException')->willReturnCallback(
            function($m = '', $c = 0, $p = null) {
                return new InvalidArgumentException($m, $c, $p);
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
     * Tests the relation operator retrieval method with an OR expression to assert whether the correct relation
     * operator is returned.
     *
     * @since [*next-version*]
     */
    public function testGetWpQueryRelationOperatorOr()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression(BooleanTypeInterface::T_OR);

        $this->assertEquals(
            'OR',
            $reflect->_getWpQueryRelationOperator($expression),
            'Retrieved and expected operator are not equal.'
        );
    }

    /**
     * Tests the relation operator retrieval method with an AND expression to assert whether the correct relation
     * operator is returned.
     *
     * @since [*next-version*]
     */
    public function testGetWpQueryRelationOperatorAnd()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression(BooleanTypeInterface::T_AND);

        $this->assertEquals(
            'AND',
            $reflect->_getWpQueryRelationOperator($expression),
            'Retrieved and expected operator are not equal.'
        );
    }

    /**
     * Tests the relation operator retrieval method with an invalid expression to assert whether an exception is thrown.
     *
     * @since [*next-version*]
     */
    public function testGetWpQueryRelationOperatorInvalid()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $this->setExpectedException('InvalidArgumentException');

        $expression = $this->createLogicalExpression(uniqid('invalid-'));
        $reflect->_getWpQueryRelationOperator($expression);
    }
}
