<?php


namespace EntityToExcel\Services;


use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\DocParser;

class PropertyReader
{
    private $property;

    const MANY_TO_ONE  = 'Doctrine\ORM\Mapping\ManyToOne';
    const MANY_TO_MANY = 'Doctrine\ORM\Mapping\ManyToMany';
    const ONE_TO_MANY  = 'Doctrine\ORM\Mapping\OneToMany';
    const ONE_TO_ONE   = 'Doctrine\ORM\Mapping\OneToOne';
    const COLUMN       = 'Doctrine\ORM\Mapping\Column';
    const EtEx         = 'App\Command\EntityToExcel\Annotations\EtEx';

    /**
     * @var AnnotationReader
     */
    private $reader;
    /**
     * @var DocParser
     */
    private $parser;

    public function __construct(\ReflectionProperty $property)
    {
        $this->parser   = new DocParser();
        $this->reader   = new AnnotationReader($this->parser);
        $this->property = $property;
    }

    public function isExcluded()
    {
        if ($this->reader->getPropertyAnnotation($this->property, self::EtEx)) {
            return $this->reader->getPropertyAnnotation($this->property, self::EtEx)->exclude;
        }
        return false;
    }

    public function includeFields()
    {
        if ($this->reader->getPropertyAnnotation($this->property, self::EtEx)) {
            return $this->reader->getPropertyAnnotation($this->property, self::EtEx)->includeFields;
        }
        return false;
    }

    public function getList()
    {
        if ($this->reader->getPropertyAnnotation($this->property, self::EtEx)) {
            return $this->reader->getPropertyAnnotation($this->property, self::EtEx)->list;
        }
        return null;
    }

    public function getFieldNameFromEntity()
    {
        if ($this->reader->getPropertyAnnotation($this->property, self::EtEx)) {
            return $this->reader->getPropertyAnnotation($this->property, self::EtEx)->listFromEntityWith;
        }
        return null;
    }

    public function getDefaultValue()
    {
        if ($this->reader->getPropertyAnnotation($this->property, self::EtEx)) {
            return $this->reader->getPropertyAnnotation($this->property, self::EtEx)->defaultValue;
        }
        return null;
    }

    public function getReverseColumn()
    {
        if ($this->reader->getPropertyAnnotation($this->property, self::EtEx)) {
            return $this->reader->getPropertyAnnotation($this->property, self::EtEx)->reverseColumn;
        }
        return null;
    }


    public function getDropDownEntity()
    {
        return $this->getAnnotation()->targetEntity;
    }


    public function isColumn(): bool
    {
        return null !== $this->reader->getPropertyAnnotation($this->property, self::COLUMN);
    }

    public function isRelation(): bool
    {
        return (
            null !== $this->reader->getPropertyAnnotation($this->property, self::MANY_TO_ONE) ||
            null !== $this->reader->getPropertyAnnotation($this->property, self::MANY_TO_MANY) ||
            null !== $this->reader->getPropertyAnnotation($this->property, self::ONE_TO_MANY) ||
            null !== $this->reader->getPropertyAnnotation($this->property, self::ONE_TO_ONE)
            );
    }

    public function getAnnotation()
    {
        if (null !== $this->reader->getPropertyAnnotation($this->property, self::COLUMN)) {
            return $this->reader->getPropertyAnnotation($this->property, self::COLUMN);
        }

        if (null !== $this->reader->getPropertyAnnotation($this->property, self::MANY_TO_ONE)) {
            return $this->reader->getPropertyAnnotation($this->property, self::MANY_TO_ONE);
        }

        if (null !== $this->reader->getPropertyAnnotation($this->property, self::MANY_TO_MANY)) {
            return $this->reader->getPropertyAnnotation($this->property, self::MANY_TO_MANY);
        }

        if (null !== $this->reader->getPropertyAnnotation($this->property, self::ONE_TO_MANY)) {
            return $this->reader->getPropertyAnnotation($this->property, self::ONE_TO_MANY);
        }

        if (null !== $this->reader->getPropertyAnnotation($this->property, self::ONE_TO_ONE)) {
            return $this->reader->getPropertyAnnotation($this->property, self::ONE_TO_ONE);
        }
    }

    public function getEtEx()
    {
        if (null !== $this->reader->getPropertyAnnotation($this->property, self::EtEx)) {
            return $this->reader->getPropertyAnnotation($this->property, self::EtEx);
        }
        return null;
    }
}
