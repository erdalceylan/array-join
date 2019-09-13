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
     * @var Field[]
     */
    private $fields = [];
    /**
     * @var \Closure
     */
    private $closure;

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param Field $field
     * @return Where
     */
    public function addField(Field $field): Where
    {
        $this->fields[] = $field;
        return $this;
    }

    /**
     * @param Field ...$fields
     * @return Where
     */
    public function setFields(Field ...$fields): Where
    {
        $this->fields = $fields;
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
        $params = [];

        foreach ($this->fields as $field) {

            $tmpParam = null;

           if (array_key_exists($field->getAlias(), $row)) {
               if(array_key_exists($field->getField(), $row[$field->getAlias()])){
                   $tmpParam = $row[$field->getAlias()][$field->getField()];
               }
           }

           $params[] = $tmpParam;
        }

        return (bool) $this->getClosure()(...$params);
    }

}
