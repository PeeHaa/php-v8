/*
  +----------------------------------------------------------------------+
  | This file is part of the pinepain/php-v8 PHP extension.              |
  |                                                                      |
  | Copyright (c) 2015-2016 Bogdan Padalko <pinepain@gmail.com>          |
  |                                                                      |
  | Licensed under the MIT license: http://opensource.org/licenses/MIT   |
  |                                                                      |
  | For the full copyright and license information, please view the      |
  | LICENSE file that was distributed with this source or visit          |
  | http://opensource.org/licenses/MIT                                   |
  +----------------------------------------------------------------------+
*/

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php_v8_int32.h"
#include "php_v8_integer.h"
#include "php_v8_value.h"
#include "php_v8_isolate.h"
#include "php_v8.h"

zend_class_entry *php_v8_int32_class_entry;
#define this_ce php_v8_int32_class_entry


static PHP_METHOD(V8Int32, __construct) {
    zval *php_v8_isolate_zv;

    zend_long value = 0;

    if (zend_parse_parameters(ZEND_NUM_ARGS(), "ol", &php_v8_isolate_zv, &value) == FAILURE) {
        return;
    }

    PHP_V8_VALUE_CONSTRUCT(getThis(), php_v8_isolate_zv, php_v8_isolate, php_v8_value)

    PHP_V8_CHECK_INT32_RANGE(value, "Int32 value to set is out of range");

    v8::Local<v8::Int32> local_value = v8::Int32::New(isolate, (int32_t) value).As<v8::Int32>();

    PHP_V8_THROW_VALUE_EXCEPTION_WHEN_EMPTY(local_value, "Failed to create Int32 value");

    php_v8_value->persistent->Reset(isolate, local_value);
}


static PHP_METHOD(V8Int32, Value) {
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    PHP_V8_VALUE_FETCH_WITH_CHECK(getThis(), php_v8_value);
    PHP_V8_ENTER_ISOLATE(php_v8_value->php_v8_isolate);

    v8::Local<v8::Value> local_value = php_v8_value_get_value_local(isolate, php_v8_value);
    v8::Local<v8::Int32> local_number = v8::Local<v8::Int32>::Cast(local_value);

    RETVAL_LONG(local_number->Value());
}


ZEND_BEGIN_ARG_INFO_EX(arginfo_v8_int32___construct, ZEND_SEND_BY_VAL, ZEND_RETURN_VALUE, 2)
                ZEND_ARG_OBJ_INFO(0, isolate, V8\\Isolate, 0)
                ZEND_ARG_TYPE_INFO(0, value, IS_LONG, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_v8_int32_Value, ZEND_RETURN_VALUE, 0, IS_LONG, NULL, 0)
ZEND_END_ARG_INFO()


static const zend_function_entry php_v8_int32_methods[] = {
        PHP_ME(V8Int32, __construct, arginfo_v8_int32___construct, ZEND_ACC_PUBLIC | ZEND_ACC_CTOR)
        PHP_ME(V8Int32, Value, arginfo_v8_int32_Value, ZEND_ACC_PUBLIC)
        PHP_FE_END
};


PHP_MINIT_FUNCTION(php_v8_int32) {
    zend_class_entry ce;
    INIT_NS_CLASS_ENTRY(ce, PHP_V8_NS, "Int32Value", php_v8_int32_methods);
    this_ce = zend_register_internal_class_ex(&ce, php_v8_integer_class_entry);

    return SUCCESS;
}


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
