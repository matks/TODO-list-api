<?php

namespace TODOListApi\Application;

class ParametersConfigHandler
{
    /**
     * @param array  $parameters
     * @param string $configAsAString
     *
     * @return string
     */
    public static function replaceParametersInConfig(array $parameters, $configAsAString)
    {
        $configAsAString = str_replace(
            self::computePlaceHolders(array_keys($parameters)),
            self::stringify(array_values($parameters)),
            $configAsAString
        );

        return $configAsAString;
    }

    /**
     * @param array $parameterKeys
     *
     * @return array
     */
    private static function computePlaceHolders(array $parameterKeys)
    {
        $placeholders = array_map(function ($key) {
            return '%'.$key.'%';
        }, $parameterKeys);

        return $placeholders;
    }

    /**
     * @param array $parameterValues
     *
     * @return string[]
     */
    private static function stringify(array $parameterValues)
    {
        $stringifiedParameters = [];

        foreach ($parameterValues as $key => $value) {
            if (is_object($value)) {
                throw new \RuntimeException('Cannot stringify an object');
            }
            if (is_array($value)) {
                $stringifiedSubObject = self::stringify($value);
                $stringifiedParameters[$key] = "['".implode("', '", $stringifiedSubObject)."']";
            } else {
                $stringifiedParameters[$key] = $value;
            }
        }

        return $stringifiedParameters;
    }
}
