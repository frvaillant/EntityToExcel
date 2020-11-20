<?php


namespace EntityToExcel\Services;


use Doctrine\Common\Annotations\AnnotationReader;

class DataTransformer
{
    /**
     * This method creates an array of useful properties to make excel file from a list of entity properties
     * @param $properties
     * @return array
     */
    public static function makePropertiesArray($properties)
    {
        foreach ($properties as $property) {

            $reader = new PropertyReader($property);

            if ($reader->isExcluded() === false) {

                $annotation = $reader->getAnnotation();
                if ($reader->isColumn()) {
                    $arrayOfProperties[] = [
                        'name'           => $property->getName(),
                        'type'           => $annotation->type,
                        'list'           => $reader->getList(),
                        'listFromEntity' => ($reader->getFieldNameFromEntity()) ? $reader->getDropDownEntity() : null,
                        'fieldName'      => ($reader->getFieldNameFromEntity()) ? $reader->getFieldNameFromEntity() : null,
                        'defaultValue'   => $reader->getDefaultValue() ?? null,
                        'reverseColumn'  => $reader->getReverseColumn() ?? null,
                    ];
                }

                if ($reader->isRelation()) {
                    $arrayOfProperties[] = [
                        'name'           => $property->getName(),
                        'type'           => 'relation',
                        'class'          => $annotation->targetEntity,
                        'list'           => $reader->getList(),
                        'includeFields'  => $reader->includeFields(),
                        'listFromEntity' => ($reader->getFieldNameFromEntity()) ? $reader->getDropDownEntity() : null,
                        'fieldName'      => ($reader->getFieldNameFromEntity()) ? $reader->getFieldNameFromEntity() : null,
                        'defaultValue'   => $reader->getDefaultValue() ?? null,
                        'reverseColumn'  => $reader->getReverseColumn() ?? null,
                    ];

                    if ($reader->includeFields()) {
                        $subProperties = DataTransformer::makePropertiesArrayFromClass($annotation->targetEntity);
                        $arrayOfProperties[$annotation->targetEntity] = $subProperties;
                    }
                }

            }
        }
        return $arrayOfProperties;
    }

    /**
     * This method makes an array of useful properties from a class Name to create excel file
     * @param $class
     * @return array
     */
    public static function makePropertiesArrayFromClass($class)
    {
        $properties = PropertyExtractor::extractProperties($class);
        $arrayOfProperties = [];

        foreach ($properties as $property) {

            $reader = new PropertyReader($property);

            if ($reader->isExcluded() === false) {

                $annotation = $reader->getAnnotation();
                if ($reader->isColumn()) {
                    $arrayOfProperties[] = [
                        'name'           => $property->getName(),
                        'type'           => $annotation->type,
                        'list'           => $reader->getList(),
                        'listFromEntity' => ($reader->getFieldNameFromEntity()) ? $reader->getDropDownEntity() : null,
                        'fieldName'      => ($reader->getFieldNameFromEntity()) ? $reader->getFieldNameFromEntity() : null,
                        'defaultValue'   => $reader->getDefaultValue() ?? null,
                        'reverseColumn'  => $reader->getReverseColumn() ?? null,
                    ];
                }

                if ($reader->isRelation()) {
                    $arrayOfProperties[] = [
                        'name'           => $property->getName(),
                        'type'           => 'relation',
                        'class'          => $annotation->targetEntity,
                        'list'           => $reader->getList(),
                        'includeFields'  => $reader->includeFields(),
                        'listFromEntity' => ($reader->getFieldNameFromEntity()) ? $reader->getDropDownEntity() : null,
                        'fieldName'      => ($reader->getFieldNameFromEntity()) ? $reader->getFieldNameFromEntity() : null,
                        'defaultValue'   => $reader->getDefaultValue() ?? null,
                        'reverseColumn'  => $reader->getReverseColumn() ?? null,
                    ];
                }

            }
        }
        return $arrayOfProperties;
    }

    /**
     * @param array $data comes from a findAll request on entity
     * @param string $fieldName the name of the field you want to use in the result array
     * @return array [fieldNameValue1, fieldNameValue2 ...]
     */
    public static function makeListFromEntity(array $data, string $fieldName)
    {
        $list = [];
        foreach ($data as $datum) {
            $getterName = 'get' . ucfirst($fieldName);
            $list[] = $datum->{$getterName}();
        }
        return $list;
    }

}
