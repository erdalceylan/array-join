<?php
/**
 * Created by IntelliJ IDEA.
 * User: erdal
 * Date: 25.10.2017
 * Time: 10:21
 */

namespace ArrayJoin;


/**
 * Class Where
 * @package Join
 */
class Where
{
    /**
     * @var Field
     */
    private $fieldOne;
    /**
     * @var Field
     */
    private $fieldTwo;
    /**
     * @var \Closure
     */
    private $closure;

    /**
     * @return Field
     */
    public function getFieldOne(): Field
    {
        return $this->fieldOne;
    }

    /**
     * @param Field $fieldOne
     * @return Where
     */
    public function setFieldOne(Field $fieldOne): Where
    {
        $this->fieldOne = $fieldOne;
        return $this;
    }

    /**
     * @return Field
     */
    public function getFieldTwo(): Field
    {
        return $this->fieldTwo;
    }

    /**
     * @param Field $fieldTwo
     * @return Where
     */
    public function setFieldTwo(Field $fieldTwo): Where
    {
        $this->fieldTwo = $fieldTwo;
        return $this;
    }

    /**
     * @return \Closure
     */
    public function getClosure(): \Closure
    {
        return $this->closure;
    }

    /**
     * @param \Closure $closure
     * @return Where
     */
    public function setClosure(\Closure $closure): Where
    {
        $this->closure = $closure;
        return $this;
    }

    /**
     * @param array $row
     * @return bool
     */
    public function is(Array $row)
    {
        $param1 = null;
        $param2 = null;

        if($this->getFieldOne() instanceof  Field && array_key_exists($this->getFieldOne()->getAlias(), $row)){
            if(array_key_exists($this->getFieldOne()->getField(), $row[$this->getFieldOne()->getAlias()])){
                $param1 = $row[$this->getFieldOne()->getAlias()][$this->getFieldOne()->getField()];
            }
        }

        if($this->getFieldTwo() instanceof  Field && array_key_exists($this->getFieldTwo()->getAlias(), $row)){
            if(array_key_exists($this->getFieldTwo()->getField(), $row[$this->getFieldTwo()->getAlias()])){
                $param2 = $row[$this->getFieldTwo()->getAlias()][$this->getFieldTwo()->getField()];
            }
        }

        return (bool) $this->getClosure()($param1, $param2);
    }

}