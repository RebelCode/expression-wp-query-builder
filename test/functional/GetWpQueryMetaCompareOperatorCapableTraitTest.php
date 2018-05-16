<?php

namespace RebelCode\WordPress\Query\Builder\FuncTest;

use Dhii\Expression\LogicalExpressionInterface;
use Dhii\Storage\Resource\Sql\Expression\SqlRelationalTypeInterface;
use OutOfRangeException;
use PHPUnit_Framework_MockObject_MockObject;
use Xpmock\TestCase;

/**
 * Tests {@see \RebelCode\WordPress\Query\Builder\GetWpQueryMetaCompareOperatorCapableTrait}.
 *
 * @since [*next-version*]
 */
class GetWpQueryMetaCompareOperatorCapableTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\WordPress\Query\Builder\GetWpQueryMetaCompareOperatorCapableTrait';

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
                                    '_createOutOfRangeException',
                                    '_normalizeString'
                                ]
                            )
                        );

        $mock = $builder->getMockForTrait();
        $mock->method('__')->willReturnArgument(0);
        $mock->method('_createOutOfRangeException')->willReturnCallback(
            function($m, $c, $p) {
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
     * Tests the meta compare operator retrieval method with a valid expression type to assert whether the correct
     * corresponding operator is retrieved.
     *
     * @since [*next-version*]
     */
    public function testGetWpQueryMetaCompareOperator()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression(SqlRelationalTypeInterface::T_BETWEEN, []);

        $this->assertEquals(
            'BETWEEN',
            $reflect->_getWpQueryMetaCompareOperator($expression),
            'Retrieved and expected operators are not equal.'
        );
    }

    /**
     * Tests the meta compare operator retrieval method with a valid negated expression type to assert whether the
     * correct corresponding negated operator is retrieved.
     *
     * @since [*next-version*]
     */
    public function testGetWpQueryMetaCompareOperatorNegated()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression(SqlRelationalTypeInterface::T_BETWEEN, [], true);

        $this->assertEquals(
            'NOT BETWEEN',
            $reflect->_getWpQueryMetaCompareOperator($expression),
            'Retrieved and expected operators are not equal.'
        );
    }

    /**
     * Tests the meta compare operator retrieval method with an invalid expression type to assert whether an exception
     * is thrown.
     *
     * @since [*next-version*]
     */
    public function testGetWpQueryMetaCompareOperatorInvalid()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $this->setExpectedException('OutOfRangeException');

        $expression = $this->createLogicalExpression(uniqid('invalid-'), []);

        $reflect->_getWpQueryMetaCompareOperator($expression);
    }
}
