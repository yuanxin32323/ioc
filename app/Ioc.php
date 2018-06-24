<?php

namespace Lisao\Ioc;

/**
 * IOC容器
 */
class Ioc {

    /**
     * 单例模式的注入类实例
     * @param array
     */
    static public $singletonInstances = [];

    /**
     * 绑定单例
     * @param string $className 类名
     * @param object $classInstance 实例
     */
    private static function bindSingleton($className, $classInstance) {
        if (is_object($classInstance)) {
            static::$singletonInstances[$className] = $classInstance;
        } else {
            throw new Exception('not object');
        }
    }

    /**
     * 获得实例
     * @param string $className 类名
     * @return object 实例
     */
    public static function getInstance($className) {
        $paramArr = self::getMethodParams($className);

        $objDetail = new \ReflectionClass($className);
        //检查是否已经实例化
        if (array_key_exists($objDetail->name, self::$singletonInstances)) {
            return self::$singletonInstances[$objDetail->name];
        } else {
            $obj = $objDetail->newInstanceArgs($paramArr);
            self::bindSingleton($objDetail->name, $obj);
        }
        return $obj;
    }

    /**
     * 执行类方法
     * @param string $className 类名
     * @param string $methodName 方法名
     * @param array $params 参数
     * @return type 执行结果
     */
    public static function make($className, $methodName, $params = []) {

        // 获取类的实例
        $instance = self::getInstance($className);

        // 获取该方法所需要依赖注入的参数
        $paramArr = self::getMethodParams($className, $methodName);

        return $instance->{$methodName}(...array_merge($paramArr, $params));
    }

    /**
     * 获得类的方法参数
     * @param string $className 类名
     * @param string $methodsName 方法名
     * @return array 参数数组
     */
     
    private static function getMethodParams($className, $methodsName = '__construct') {

        // 通过反射获得该类
        $class = new \ReflectionClass($className);
        $paramArr = []; // 记录参数，和参数类型
        // 判断该类是否有构造函数
        if ($class->hasMethod($methodsName)) {
            // 获得构造函数
            $construct = $class->getMethod($methodsName);

            // 判断构造函数是否有参数
            $params = $construct->getParameters();

            if (count($params) > 0) {

                // 判断参数类型
                foreach ($params as $key => $param) {

                    if ($paramClass = $param->getClass()) {

                        // 获得参数类型名称
                        $paramClassName = $paramClass->getName();

                        // 获得参数类型
                        $paramArr[] = self::getInstance($paramClassName);
                    }
                }
            }
        }

        return $paramArr;
    }

}
