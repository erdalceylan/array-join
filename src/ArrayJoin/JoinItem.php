<?php
/**
 * Created by IntelliJ IDEA.
 * User: erdal
 * Date: 19.10.2017
 * Time: 13:13
 */

namespace ArrayJoin;


/**
 * Class JoinItem
 * @package Join
 */
class JoinItem
{
    const TYPE_INNER_JOIN = 1;
    const TYPE_LEFT_JOIN = 2;
    const TYPE_RIGHT_JOIN = 3;
    const TYPE_FROM = 4;
    /**
     * @var string
     */
    private $alias;
    /**
     * @var int
     */
    private $type;
    /**
     * @var $on On
     */
    private $on;
    /**
     * @var array
     */
    private $data;

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     * @return JoinItem
     */
    public function setAlias(string $alias): JoinItem
    {
        $this->alias = trim( $alias );
        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return JoinItem
     */
    public function setType(int $type): JoinItem
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return On
     */
    public function getOn()
    {
        return $this->on;
    }

    /**
     * @param On $on
     * @return JoinItem
     */
    public function setOn(On $on)
    {
        $this->on = $on;
        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param $data
     * @return JoinItem
     */
    public function setData(array $data): JoinItem
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return Field
     */
    public function getOnFieldForSelf(){

        return $this->getOn()->getFieldOne()->getAlias() == $this->getAlias()
            ? $this->getOn()->getFieldOne() : $this->getOn()->getFieldTwo();
    }

    /**
     * @return Field
     */
    public function getOnFieldForJoin(){

        return $this->getOn()->getFieldOne()->getAlias() != $this->getAlias()
            ? $this->getOn()->getFieldOne() : $this->getOn()->getFieldTwo();
    }

}
