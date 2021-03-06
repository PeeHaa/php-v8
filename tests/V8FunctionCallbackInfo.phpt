--TEST--
V8\FunctionCallbackInfo
--SKIPIF--
<?php if (!extension_loaded("v8")) {
    print "skip";
} ?>
--FILE--
<?php

/** @var \Phpv8Testsuite $helper */
$helper = require '.testsuite.php';

require '.tracking_dtors.php';

$isolate1 = new v8Tests\TrackingDtors\Isolate();
$extensions1 = [];

$global_template1 = new V8\ObjectTemplate($isolate1);

$context1 = new V8\Context($isolate1, $extensions1, $global_template1);

// TEST: Pass context instead of isolate to FunctionTemplate

$scalar = new \V8\StringValue($isolate1, "test");
$object = new \V8\ObjectValue($context1);

/** @var V8\FunctionCallbackInfo $callback_info */
$callback_info = null;

$func = new v8Tests\TrackingDtors\FunctionObject($context1, function (V8\FunctionCallbackInfo $info) use ($helper, &$callback_info, $scalar, $object, $isolate1, $context1) {
    echo 'Function called', PHP_EOL;

    $helper->header('Object representation');
    $helper->dump($info);
    $helper->space();

    $callback_info = $info;
    $helper->assert('Original arguments number passed', $info->Length() == 2);

    $helper->assert('Callback info holds original isolate object', $info->GetIsolate(), $isolate1);
    $helper->assert('Callback info holds original isolate object', $info->GetContext(), $context1);
    $helper->assert('Callback info object is in context', $info->InContext());

    // scalars hold no info about their zval, so they their zvals are recreated on each access
    $helper->value_matches_with_no_output($scalar, $info->Arguments()[0]); // shouldn't match
    // while objects can hold info about their zval and keep it untill zval's free() object handler not called (or that info will not be cleaned manually)
    $helper->value_matches_with_no_output($object, $info->Arguments()[1]); // will match
});

$context1->GlobalObject()->Set($context1, new \V8\StringValue($isolate1, 'print'), $func);
$context1->GlobalObject()->Set($context1, new \V8\StringValue($isolate1, 'scalar'), $scalar);
$context1->GlobalObject()->Set($context1, new \V8\StringValue($isolate1, 'obj'), $object);

$source1 = 'print(scalar, obj); "Script done";';
$file_name1 = 'test.js';


$script1 = new V8\Script($context1, new \V8\StringValue($isolate1, $source1), new \V8\ScriptOrigin($file_name1));

$helper->dump($script1->Run($context1)->ToString($context1)->Value());

$helper->space();

$helper->assert('Callback info object is out of context', false === $callback_info->InContext());
try {
    $callback_info->GetReturnValue();
} catch (Exception $e) {
    $helper->exception_export($e);
}
$helper->line();

$helper->header('Object representation (outside of context)');
$helper->dump($callback_info);
$helper->space();


echo 'We are done for now', PHP_EOL;

?>
--EXPECT--
Function called
Object representation:
----------------------
object(V8\FunctionCallbackInfo)#13 (0) {
}


Original arguments number passed: ok
Callback info holds original isolate object: ok
Callback info holds original isolate object: ok
Callback info object is in context: ok
Expected value is not identical to actual value
Expected value is identical to actual value
string(11) "Script done"


Callback info object is out of context: ok
V8\Exceptions\GenericException: Attempt to use callback info object out of callback context

Object representation (outside of context):
-------------------------------------------
object(V8\FunctionCallbackInfo)#13 (0) {
}


We are done for now
FunctionObject dies now!
Isolate dies now!
