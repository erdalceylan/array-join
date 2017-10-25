<?php
/**
 * Created by IntelliJ IDEA.
 * User: erdal
 * Date: 19.10.2017
 * Time: 13:36
 */

namespace ArrayJoin;


/**
 * Class On
 * @package Join
 */
class On
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
     * On constructor.
     * @param string $fieldEquals
     * @throws \Exception
     */
    function __construct(string $fieldEquals)
    {
        $exploded = explode("=", preg_replace('/\s+/', '', $fieldEquals) );

        if(count($exploded) == 2){
             $this
                ->setFieldOne(Field::parse($exploded[0]))
                ->setFieldTwo(Field::parse($exploded[1]));
        }else{
            throw new \Exception("incorrect value for '$fieldEquals' should be 'table1.field = table2.field'");
        }
    }

    /**
     * @return Field
     */
    public function getFieldOne(): Field
    {
        return $this->fieldOne;
    }

    /**
     * @param Field $fieldOne
     * @return On
     */
    public function setFieldOne(Field $fieldOne): On
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
     * @return On
     */
    public function setFieldTwo(Field $fieldTwo): On
    {
        $this->fieldTwo = $fieldTwo;
        return $this;
    }

    /**
     * @param string $alias
     * @return Field
     * @throws \Exception
     */
    public function getFieldForAlias(string $alias)
    {
        if($this->getFieldOne()->getAlias() == $alias){

            return$this->getFieldOne();

        }else if ($this->getFieldTwo()->getAlias() == $alias){

            return $this->getFieldTwo();
        }else{

            throw new \Exception("Please check joın on parameter");
        }
    }

}