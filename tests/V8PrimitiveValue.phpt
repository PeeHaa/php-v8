--TEST--
v8\PrimitiveValue
--SKIPIF--
<?php if (!extension_loaded("v8")) print "skip"; ?>
--FILE--
<?php

/** @var \Phpv8Testsuite $helper */
$helper = require '.testsuite.php';

require '.v8-helpers.php';
$v8_helper = new PhpV8Helpers($helper);

// Tests:


$isolate = new v8\Isolate();
$value = new v8\PrimitiveValue($isolate);


$helper->header('Object representation');
debug_zval_dump($value);
$helper->space();

$helper->assert('PrimitiveValue extends Value', $value instanceof \v8\Value);
$helper->line();

$helper->header('Accessors');
$helper->method_matches($value, 'GetIsolate', $isolate);
$helper->space();

$v8_helper->run_checks($value);

$extensions = [];
$global_template = new \v8\ObjectTemplate($isolate);
$context = new \v8\Context($isolate, $extensions, $global_template);


$helper->header('Primitive converters');
$helper->method_export($value, 'BooleanValue', [$context]);
$helper->method_export($value, 'NumberValue', [$context]);
$helper->space();


$string = $value->ToString($context);

$helper->header(get_class($value) .'::ToString() converting');
debug_zval_dump($string);
debug_zval_dump($string->Value());
$helper->space();

$v8_helper->run_checks($value, 'Checkers after ToString() converting');

$helper->header(get_class($value) .'::ToObject() converting');
try {
  $object = $value->ToObject($context);
} catch (Exception $e) {
  $helper->exception_export($e);
}
$helper->space();


?>
--EXPECT--
Object representation:
----------------------
object(v8\PrimitiveValue)#4 (1) refcount(2){
  ["isolate":"v8\Value":private]=>
  object(v8\Isolate)#3 (1) refcount(2){
    ["snapshot":"v8\Isolate":private]=>
    NULL
  }
}


PrimitiveValue extends Value: ok

Accessors:
----------
v8\PrimitiveValue::GetIsolate() matches expected value


Checks on v8\PrimitiveValue:
----------------------------
v8\PrimitiveValue(v8\Value)->IsUndefined(): bool(true)
v8\PrimitiveValue(v8\Value)->IsNull(): bool(false)
v8\PrimitiveValue(v8\Value)->IsTrue(): bool(false)
v8\PrimitiveValue(v8\Value)->IsFalse(): bool(false)
v8\PrimitiveValue(v8\Value)->IsName(): bool(false)
v8\PrimitiveValue(v8\Value)->IsString(): bool(false)
v8\PrimitiveValue(v8\Value)->IsSymbol(): bool(false)
v8\PrimitiveValue(v8\Value)->IsFunction(): bool(false)
v8\PrimitiveValue(v8\Value)->IsArray(): bool(false)
v8\PrimitiveValue(v8\Value)->IsObject(): bool(false)
v8\PrimitiveValue(v8\Value)->IsBoolean(): bool(false)
v8\PrimitiveValue(v8\Value)->IsNumber(): bool(false)
v8\PrimitiveValue(v8\Value)->IsInt32(): bool(false)
v8\PrimitiveValue(v8\Value)->IsUint32(): bool(false)
v8\PrimitiveValue(v8\Value)->IsDate(): bool(false)
v8\PrimitiveValue(v8\Value)->IsArgumentsObject(): bool(false)
v8\PrimitiveValue(v8\Value)->IsBooleanObject(): bool(false)
v8\PrimitiveValue(v8\Value)->IsNumberObject(): bool(false)
v8\PrimitiveValue(v8\Value)->IsStringObject(): bool(false)
v8\PrimitiveValue(v8\Value)->IsSymbolObject(): bool(false)
v8\PrimitiveValue(v8\Value)->IsNativeError(): bool(false)
v8\PrimitiveValue(v8\Value)->IsRegExp(): bool(false)


Primitive converters:
---------------------
v8\PrimitiveValue(v8\Value)->BooleanValue(): bool(false)
v8\PrimitiveValue(v8\Value)->NumberValue(): float(NAN)


v8\PrimitiveValue::ToString() converting:
-----------------------------------------
object(v8\StringValue)#48 (1) refcount(2){
  ["isolate":"v8\Value":private]=>
  object(v8\Isolate)#3 (1) refcount(5){
    ["snapshot":"v8\Isolate":private]=>
    NULL
  }
}
string(9) "undefined" refcount(1)


Checkers after ToString() converting:
-------------------------------------
v8\PrimitiveValue(v8\Value)->IsUndefined(): bool(true)
v8\PrimitiveValue(v8\Value)->IsNull(): bool(false)
v8\PrimitiveValue(v8\Value)->IsTrue(): bool(false)
v8\PrimitiveValue(v8\Value)->IsFalse(): bool(false)
v8\PrimitiveValue(v8\Value)->IsName(): bool(false)
v8\PrimitiveValue(v8\Value)->IsString(): bool(false)
v8\PrimitiveValue(v8\Value)->IsSymbol(): bool(false)
v8\PrimitiveValue(v8\Value)->IsFunction(): bool(false)
v8\PrimitiveValue(v8\Value)->IsArray(): bool(false)
v8\PrimitiveValue(v8\Value)->IsObject(): bool(false)
v8\PrimitiveValue(v8\Value)->IsBoolean(): bool(false)
v8\PrimitiveValue(v8\Value)->IsNumber(): bool(false)
v8\PrimitiveValue(v8\Value)->IsInt32(): bool(false)
v8\PrimitiveValue(v8\Value)->IsUint32(): bool(false)
v8\PrimitiveValue(v8\Value)->IsDate(): bool(false)
v8\PrimitiveValue(v8\Value)->IsArgumentsObject(): bool(false)
v8\PrimitiveValue(v8\Value)->IsBooleanObject(): bool(false)
v8\PrimitiveValue(v8\Value)->IsNumberObject(): bool(false)
v8\PrimitiveValue(v8\Value)->IsStringObject(): bool(false)
v8\PrimitiveValue(v8\Value)->IsSymbolObject(): bool(false)
v8\PrimitiveValue(v8\Value)->IsNativeError(): bool(false)
v8\PrimitiveValue(v8\Value)->IsRegExp(): bool(false)


v8\PrimitiveValue::ToObject() converting:
-----------------------------------------
v8\Exceptions\TryCatchException: TypeError: Cannot convert undefined or null to object