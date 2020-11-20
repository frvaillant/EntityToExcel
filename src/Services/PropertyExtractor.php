<?php


namespace EntityToExcel\Services;


class PropertyExtractor
{
    public static function extractProperties($classFQCN)
    {
        $class = new \ReflectionClass($classFQCN);
        $properties = $class->getProperties();
        return $properties;
    }
}
