<?php


namespace EntityToExcel\Annotations;

/**
 * @Annotation
 */
class EtEx
{
    /**
     * @var string
     */
    public $reverseColumn;

    /**
     * @var bool
     */
    public $exclude = false;

    /**
     * @var array
     */
    public $list = null;

    /**
     * @var bool
     */
    public $includeFields = false;

    /**
     * @var string
     */
    public $listFromEntityWith = "";

    /**
     * @var string
     */
    public $defaultValue = '';
}
