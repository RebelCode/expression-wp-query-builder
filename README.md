# RebelCode - Expression WP Query Builder - Abstract

[![Build Status](https://travis-ci.org/RebelCode/expression-wp-query-builder-abstract.svg?branch=develop)](https://travis-ci.org/RebelCode/expression-wp-query-builder-abstract)
[![Code Climate](https://codeclimate.com/github/RebelCode/expression-wp-query-builder-abstract/badges/gpa.svg)](https://codeclimate.com/github/RebelCode/expression-wp-query-builder-abstract)
[![Test Coverage](https://codeclimate.com/github/RebelCode/expression-wp-query-builder-abstract/badges/coverage.svg)](https://codeclimate.com/github/RebelCode/expression-wp-query-builder-abstract/coverage)
[![Latest Stable Version](https://poser.pugx.org/rebelcode/expression-wp-query-builder-abstract/version)](https://packagist.org/packages/rebelcode/expression-wp-query-builder-abstract)

Abstract functionality for building `WP_Query` args using expressions.

## Details

This package provides abstract functionality for the most implementation aspects of building [`WP_Query`] arguments from
expressions. The traits in this package are meant to complement each other, while also remaining agnostic of the each
other's implementation details. Most, if not all, traits are designed to provide functionality that depends on
abstracted methods. Other traits in the package will offer implementations for those abstracted methods, while also
depending on their own abstracted methods.
 
### Traits

#### [`BuildWpQueryArgsCapableTrait`]

**:eye: Build Args**

Intended to provide the entry point functionality of building an expression into [`WP_Query`] args by attempting to
build each expression term as either a comparison, meta query relation entry or taxonomy query relation entry. 
 
- **Required implementations:**
  - `_buildWpQueryCompare()` - _fulfilled by [`BuildWpQueryCompareCapableTrait`](#buildwpquerycomparecapabletrait)_
  - `_buildWpQueryMetaRelation()` - _fulfilled indirectly by [`BuildWpQueryRelationTermCapableTrait`](#buildwpqueryrelationtermcapabletrait)_
  - `_buildWpQueryTaxTelation()` - _fulfilled indirectly by [`BuildWpQueryRelationTermCapableTrait`](#buildwpqueryrelationtermcapabletrait)_

----

#### [`BuildWpQueryCompareCapableTrait`]

**:eye: Build Comparison**

Provides functionality for building top-level comparison key-value pairs.
 
- **Required implementations:**
  - `_getWpQueryCompareKey()`
  - `_getWpQueryCompareValue()`
- **Complements:**
  - [`BuildWpQueryArgsCapableTrait`](#buildwpqueryargscapabletrait)

----

#### [`BuildWpQueryRelationCapableTrait`]

**:eye: Build Relation**

Provides functionality for building relation arrays.

- **Required implementations:**
  - `_getWpQueryRelationOperator()` - _fullfilled by [`GetWpQueryRelationOperatorCapableTrait`](#getwpqueryrelationoperatorcapabletrait)_
  - `_buildWpQueryRelationTerm()` - _fulfilled by [`BuildWpQueryRelationTermCapableTrait`](#buildwpqueryrelationtermcapabletrait)_
- **Complements:**
  - [`BuildWpQueryRelationTermCapableTrait`](#buildwpqueryrelationtermcapabletrait)

----

#### [`BuildWpQueryRelationTermCapableTrait`]

**:eye: Build Relation Term**

Provides functionality for building the terms in a relation array, by delegating building mechanism used depending on the current relation context, i.e. `meta_query` relation or `tax_query` relation.

- **Required implementations:**
  - `_buildWpQueryMetaCompare()` - _fulfilled by [`BuildWpQueryMetaCompareCapableTrait`]_
  - `_buildWpQueryTaxCompare()` - _fulfilled by [`BuildWpQueryTaxCompareCapableTrait`]_
- **Complements**
  - [`BuildWpQueryRelationTermCapableTrait`](#buildwpqueryrelationtermcapabletrait)

----

#### [`BuildWpQueryMetaCompareCapableTrait`]

**:eye: Build Meta Comparison**

Provides functionality for building meta comparison arrays.

- **Required implementations:**
  - `_getWpQueryMetaCompareKey()`
  - `_getWpQueryMetaCompareValue()`
  - `_getWpQueryMetaCompareType()` - _fulfilled by [`GetWpQueryMetaCompareTypeCapableTrait`]_
  - `_getWpQueryMetaCompareOperator()` - _fulfilled by [`GetWpQueryMetaCompareOperatorCapableTrait`]_
- **Complements:**
  - [`BuildWpQueryRelationTermCapableTrait`](#buildwpqueryrelationtermcapabletrait)

---

#### [`BuildWpQueryTaxCompareCapableTrait`]

**:eye: Build Taxonomy Comparison**

Provides functionality for building taxonomy comparison arrays.

- **Required implementations:**
  - `_getWpQueryTaxCompareTaxonomy()`
  - `_getWpQueryTaxCompareField()`
  - `_getWpQueryTaxCompareTerms()`
  - `_getWpQueryTaxCompareOperator()` - _fulfilled by [`GetWpQueryTaxCompareOperatorCapableTrait`](#getwpquerytaxcompareoperatorcapabletrait)_
- **Complements:**
  - [`BuildWpQueryRelationTermCapableTrait`](#buildwpqueryrelationtermcapabletrait)

---

#### [`GetWpQueryMetaCompareOperatorCapableTrait`]

**:eye: Get Meta Comparison Operator**

Provides functionality for resolving the meta comparison compare type from an expression.

- **Complements:**
  - [`BuildWpQueryMetaCompareCapableTrait`](#buildwpquerymetacomparecapabletrait)

---

#### [`GetWpQueryMetaCompareTypeCapableTrait`]

**:eye: Get Meta Comparison Type**

Provides functionality for resolving the meta comparison value cast type from an expression.

- **Required implementations:**
  - `_getWpQueryMetaCompareValue()`
- **Complements:**
  - [`BuildWpQueryMetaCompareCapableTrait`](#buildwpquerymetacomparecapabletrait)

---

#### [`GetWpQueryTaxCompareOperatorCapableTrait`]

**:eye: Get Taxonomy Comparison Operator**

Provides functionality for resolving the taxonomy comparison operator from an expression.

- **Complements:**
  - [`BuildWpQueryTaxCompareCapableTrait`](#buildwpquerytaxcomparecapabletrait)

---

#### [`GetWpQueryRelationOperatorCapableTrait`]

**:eye: Get Relation Operator**

Provides functionality for resolving the relation operator ("AND" or "OR") from an expression.

- **Complements:**
  - [`BuildWpQueryRelationCapableTrait`](#buildwpqueryrelationcapabletrait)

---

If all of the above traits are brought together, the implementing class is only required to implement the following:

- `_buildWpQueryMetaRelation()` - recommended to redirect to `buildWpQueryRelation` with a "meta" mode.
- `_buildWpQueryTaxRelation()` - recommended to redirect to `buildWpQueryRelation` with a "tax" mode.
- `_getWpQueryCompareKey()` - recommended to search the expression for a [`VariableTermInterface`] or an 
[`EntityFieldInterface`] and retrieve the field.
- `_getWpQueryCompareValue()` - recommended to search the expression for a [`LiteralTermInterface`].
- `_getWpQueryMetaCompareKey()` - recommended to search the expression for a [`VariableTermInterface`] or
an [`EntityFieldInterface`] and retrieve the field.
- `_getWpQueryMetaCompareValue()` - recommended to search the expression for a [`LiteralTermInterface`].
- `_getWpQueryTaxCompareTaxonomy()` - recommended to search the expression for an [`EntityFieldInterface`] and retrieve
the entity.
- `_getWpQueryTaxCompareField()` - recommended to search the expression for an [`EntityFieldInterface`] and retrieve
the field.
- `_getWpQueryTaxCompareTerms()` - recommended to search the expression for a [`LiteralTermInterface`].

[`WP_Query`]: https://codex.wordpress.org/Class_Reference/WP_Query

[`BuildWpQueryArgsCapableTrait`]: src/BuildWpQueryArgsCapableTrait.php
[`BuildWpQueryCompareCapableTrait`]: src/BuildWpQueryCompareCapableTrait.php
[`BuildWpQueryRelationCapableTrait`]: src/BuildWpQueryRelationCapableTrait.php
[`BuildWpQueryRelationTermCapableTrait`]: src/BuildWpQueryRelationTermCapableTrait.php
[`BuildWpQueryMetaCompareCapableTrait`]: src/BuildWpQueryMetaCompareCapableTrait.php
[`BuildWpQueryTaxCompareCapableTrait`]: src/BuildWpQueryTaxCompareCapableTrait.php
[`GetWpQueryRelationOperatorCapableTrait`]: src/GetWpQueryRelationOperatorCapableTrait.php
[`GetWpQueryMetaCompareTypeCapableTrait`]: src/GetWpQueryMetaCompareTypeCapableTrait.php
[`GetWpQueryMetaCompareOperatorCapableTrait`]: src/GetWpQueryMetaCompareOperatorCapableTrait.php
[`GetWpQueryTaxCompareOperatorCapableTrait`]: src/GetWpQueryTaxCompareOperatorCapableTrait.php
[`GetWpQueryRelationOperatorCapableTrait`]: src/GetWpQueryRelationOperatorCapableTrait.php

[`LiteralTermInterface`]: https://github.com/Dhii/expression-interface/blob/develop/src/LiteralTermInterface.php
[`VariableTermInterface`]: https://github.com/Dhii/expression-interface/blob/develop/src/VariableTermInterface.php
[`EntityFieldInterface`]: https://github.com/Dhii/sql-interface/blob/develop/src/EntityFieldInterface.php
