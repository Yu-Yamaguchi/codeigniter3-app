<?php

if ( ! function_exists('doMethod'))
{
    /**
     * privateメソッドを実行する.
     */
    function call_reflection_function($obj, $methodName, $param)
    {
        // ReflectionClassをテスト対象のクラスをもとに作る.
        $reflection = new ReflectionClass($obj);
        // メソッドを取得する.
        $method = $reflection->getMethod($methodName);
        // アクセス許可をする.
        $method->setAccessible(true);
        // メソッドを実行して返却値をそのまま返す.
        return $method->invokeArgs($obj, $param);
    }
}