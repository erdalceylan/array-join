<?php
/**
 * Created by IntelliJ IDEA.
 * User: erdal
 * Date: 19.10.2017
 * Time: 13:52
 */

namespace ArrayJoin;


/**
 * Class Field
 * @package Join
 */
class Field
{
    /**
     * @var string
     */
    private $alias;
    /**
     * @var string
     */
    private $field;

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     * @return Field
     */
    public function setAlias(string $alias): Field
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     * @return Field
     */
    public function setField(string $field): Field
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @param \string ...$fields
     * @return Field[]
     * @throws \Exception
     */
    public static function select(string ...$fields)
    {
        $returnFields = [];

        foreach ($fields as $field){
            $returnFields[] = self::parse($field);
        }

        return $returnFields;
    }

    /**
     * @param string $TableField
     * @return Field
     * @throws \Exception
     */
    public static function parse(string $TableField) : Field
    {
        $exploded = explode( ".", preg_replace('/\s+/', '', $TableField) );

        if(count($exploded) == 2){
            return (new Field())
                ->setAlias($exploded[0])
                ->setField($exploded[1]);
        }else{
            throw new \Exception("incorrect value for '$TableField' should be 'table.field'");
        }
    }
}
