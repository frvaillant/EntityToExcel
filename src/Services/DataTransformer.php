<?php


namespace EntityToExcel\Services;


use Doctrine\Common\Annotations\AnnotationReader;

class DataTransformer
{

    private static function makeArray($property, $type, $classname, $reader)
    {
        return [
            'name'           => $property->getName(),
            'type'           => $type,
            'class'          => $classname,
            'list'           => $reader->getList(),
            'includeFields'  => $reader->includeFields() ?? null,
            'listFromEntity' => ($reader->getFieldNameFromEntity()) ? $reader->getDropDownEntity() : null,
            'fieldName'      => $reader->getFieldNameFromEntity() ?? null,
            'defaultValue'   => $reader->getDefaultValue(),
            'displayName'    => $reader->getDisplayName()
        ];
    }

    /**
     * This method creates an array of useful properties to make excel file from a list of entity properties
     * @param $properties
     * @return array
     */
    public static function makePropertiesArray($properties)
    {
        $arrayOfProperties = [];

        foreach ($properties as $property) {

            $reader = new PropertyReader($property);
            $annotation = $reader->getAnnotation();

            if ($reader->isExcluded() === false) {

                if ($reader->isColumn()) {
                    $arrayOfProperties[] = DataTransformer::makeArray($property, $annotation->type, null, $reader);
                }

                if ($reader->isRelation()) {
                    $arrayOfProperties[] = DataTransformer::makeArray($property, 'relation', $annotation->targetEntity, $reader);

                    if ($reader->includeFields()) {
                        $subclassProperties = PropertyExtractor::extractProperties($annotation->targetEntity);
                        $subProperties = DataTransformer::makePropertiesArray($subclassProperties);
                        $arrayOfProperties[$annotation->targetEntity] = $subProperties;
                    }
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
