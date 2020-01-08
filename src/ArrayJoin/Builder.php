<?php
/**
 * Created by IntelliJ IDEA.
 * User: erdal
 * Date: 19.10.2017
 * Time: 12:03
 */

namespace ArrayJoin;


/**
 * Class Builder
 * @package Join
 */
class Builder
{
    /**
     * @var int
     */
    const FETCH_TYPE_ARRAY = 1;
    /**
     * @var int
     */
    const FETCH_TYPE_OBJECT = 2;
    /**
     * @var Field[]
     */
    private $fields;
    /**
     * @var JoinItem
     */
    private $_from;
    /**
     * @var JoinItem[]
     */
    private $_join = [];
    /**
     * @var Where[]
     */
    private $_where = [];
    /**
     * @var Field[]
     */
    private $_group = [];
    /**
     * @var int
     */
    private $_limit = -1;
    /**
     * @var int
     */
    private $_offset = -1;
    /**
     * @var int
     */
    private $_fetchType;
    /**
     * @var array
     */
    private $aliases = [];

    /**
     * @return Builder
     */
    public static function newInstance() : Builder
    {
        return new Builder();
    }

    /**
     * @param \string[] $fields
     * @return Builder
     * @throws \Exception
     */
    public function select(string ...$fields ) : Builder
    {
        $this->fields = Field::select(...$fields);

        return $this;
    }

    /**
     * @param array $from
     * @param string $alias
     * @return Builder
     * @throws \Exception
     */
    public function from(array $from , string $alias) : Builder
    {
        $this->checkAliases($alias);

        $this->_from = (new JoinItem())
            ->setType(JoinItem::TYPE_FROM)
            ->setAlias($alias)
            ->setData($from);

        return $this;
    }

    /**
     * @param array $innerJoin
     * @param string $alias
     * @param On $on
     * @return Builder
     * @throws \Exception
     */
    public function innerJoin(array $innerJoin, string $alias, On $on) : Builder
    {
        $this->checkAliases($alias);

        $this->_join[] = (new JoinItem())
            ->setType(JoinItem::TYPE_INNER_JOIN)
            ->setAlias($alias)
            ->setOn($on)
            ->setData($innerJoin);

        return $this;
    }

    /**
     * @param array $leftJoin
     * @param string $alias
     * @param On $on
     * @return Builder
     * @throws \Exception
     */
    public function leftJoin(array $leftJoin, string $alias, On $on) : Builder
    {
        $this->checkAliases($alias);

        $this->_join[] = (new JoinItem())
            ->setType(JoinItem::TYPE_LEFT_JOIN)
            ->setAlias($alias)
            ->setOn($on)
            ->setData($leftJoin);

        return $this;
    }

    /**
     * @param array $rightJoin
     * @param string $alias
     * @param On $on
     * @return Builder
     * @throws \Exception
     */
    public function rightJoin(array $rightJoin, string $alias, On $on) : Builder
    {
        $this->checkAliases($alias);

        $this->_join[] = (new JoinItem())
            ->setType(JoinItem::TYPE_RIGHT_JOIN)
            ->setAlias($alias)
            ->setOn($on)
            ->setData($rightJoin);

        return $this;
    }

    /**
     * @param \Closure $closure
     * @param string ...$fields
     * @return Builder
     * @throws \Exception
     */
    public function where( \Closure $closure, string ...$fields) : Builder
    {
        $tmpWhere = new Where();

        foreach ($fields as $field) {
            $tmpWhere->addField(Field::parse($field));
        }

        $tmpWhere->setClosure($closure);

        $this->_where[] = $tmpWhere;

        return $this;
    }

    /**
     * @param string ...$fields
     * @return Builder
     * @throws \Exception
     */
    public function groupBy(string ...$fields) : Builder
    {
        foreach ($fields as $field) {
            $this->_group[] = Field::parse($field);
        }

        return $this;
    }

    /**
     * @param int $limit
     * @return Builder
     */
    public function limit( $limit ) : Builder
    {
        $this->_limit = $limit+0;
        return $this;
    }

    /**
     * @param $offset
     * @return Builder
     */
    public function offset($offset ) : Builder
    {
        $this->_offset = $offset+0;
        return $this;
    }

    /**
     * @return int
     */
    public function getFetchType(): int
    {
        return $this->_fetchType;
    }

    /**
     * @param int $fetchType
     * @return Builder
     */
    public function setFetchType(int $fetchType): Builder
    {
        $this->_fetchType = $fetchType;
        return $this;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function execute()
    {
        $returnArray = [];

        if(!is_int($this->_fetchType)){
            $this->_fetchType = self::FETCH_TYPE_OBJECT;
        }

        if( !($this->_from instanceof JoinItem)){
            throw new \Exception("Please set Builder::from(Array)");
        }

        foreach ($this->_from->getData() as  $fromRow){
            $returnArray[] = [$this->_from->getAlias() => (array)$fromRow];
        }

        foreach ($this->_join as $joinItem){
            switch ($joinItem->getType()){

                case JoinItem::TYPE_INNER_JOIN:
                    $this->_innerJoin($returnArray, $joinItem);
                    break;
                case JoinItem::TYPE_LEFT_JOIN:
                    $this->_leftJoin($returnArray, $joinItem);
                    break;
                case JoinItem::TYPE_RIGHT_JOIN:
                    $this->_rightJoin($returnArray, $joinItem);
                    break;
                default:
                    throw new \Exception("Please check Join type 'JoinItem::TYPE_*'");
                    break;
            }
        }

        foreach ($this->_where as $where){
            foreach ($returnArray as $key => $value){
                if($where->is($value)){
                    $returnArray[$key] = $value;
                }else{
                    unset($returnArray[$key]);
                }
            }
        }

        if ($this->_group) {

            $this->_groupBy($returnArray);
        }

        foreach ($returnArray as $key => $value){
            $returnArray[$key] = $this->fieldNormalize($value);
        }

        $limit = null;
        $offset = 0;
        $arrayCount = count($returnArray);

        if( $this->_offset > 0 && $this->_offset < $arrayCount ){
            $offset = $this->_offset;
        }

        if( $this->_limit > -1 && $this->_limit <= $arrayCount - $offset){
            $limit = $this->_limit;
        }

        return array_slice($returnArray, $offset, $limit);
    }

    /**
     * @param array $leftArray
     * @param JoinItem $joinItem
     */
    private function _innerJoin(array &$leftArray, JoinItem $joinItem)
    {
        $tmpArray = [];
        $onFieldForJoin = $joinItem->getOnFieldForJoin();
        $onFieldForSelf = $joinItem->getOnFieldForSelf();

        foreach ($leftArray as $key => &$value){

            if(array_key_exists($onFieldForJoin->getAlias(), $value) && array_key_exists($onFieldForJoin->getField(), $value[$onFieldForJoin->getAlias()])){

                foreach ($joinItem->getData() as $d){

                    $datum = (array) $d;

                    if( array_key_exists($onFieldForSelf->getField(), $datum)){

                        if($datum[$onFieldForSelf->getField()] == $value[$onFieldForJoin->getAlias()][$onFieldForJoin->getField()]){

                            $tmpArray[] = array_merge($value, [$onFieldForSelf->getAlias() => $datum]);
                        }
                    }
                }
            }
        }

        $leftArray = $tmpArray;
    }

    /**
     * @param array $leftArray
     * @param JoinItem $joinItem
     */
    private function _leftJoin(array &$leftArray, JoinItem $joinItem)
    {
        $tmpArray = [];
        $onFieldForJoin = $joinItem->getOnFieldForJoin();
        $onFieldForSelf = $joinItem->getOnFieldForSelf();

        foreach ($leftArray as $key => &$value){

            $foundMergedRow = false;

            if(array_key_exists($onFieldForJoin->getAlias(), $value) && array_key_exists($onFieldForJoin->getField(), $value[$onFieldForJoin->getAlias()])){

                foreach ($joinItem->getData() as $d){

                    $datum = (array) $d;

                    if( array_key_exists($onFieldForSelf->getField(), $datum)){

                        if($datum[$onFieldForSelf->getField()] == $value[$onFieldForJoin->getAlias()][$onFieldForJoin->getField()]){

                            $foundMergedRow = true;
                            $tmpArray[] = array_merge($value, [$onFieldForSelf->getAlias() => $datum]);
                        }
                    }
                }
            }

            if($foundMergedRow === false){
                $tmpArray[] = array_merge($value, [$onFieldForSelf->getAlias() => []]);
            }
        }

        $leftArray = $tmpArray;
    }

    /**
     * @param array $leftArray
     * @param JoinItem $joinItem
     */
    private function _rightJoin(array &$leftArray, JoinItem $joinItem)
    {
        $tmpArray = [];
        $onFieldForJoin = $joinItem->getOnFieldForJoin();
        $onFieldForSelf = $joinItem->getOnFieldForSelf();

        foreach ($joinItem->getData() as $d){

            $datum = (array) $d;

            $foundMergedRow = false;

            if( array_key_exists($onFieldForSelf->getField(), $datum)){

                foreach ($leftArray as $key => &$value){

                    if(array_key_exists($onFieldForJoin->getAlias(), $value) && array_key_exists($onFieldForJoin->getField(), $value[$onFieldForJoin->getAlias()])){

                        if($datum[$onFieldForSelf->getField()] == $value[$onFieldForJoin->getAlias()][$onFieldForJoin->getField()]){

                            $foundMergedRow = true;
                            $tmpArray[] = array_merge($value, [$onFieldForSelf->getAlias() => $datum]);
                        }
                    }
                }
            }

            if($foundMergedRow === false){
                $tmpArray[] = array_merge([$onFieldForJoin->getAlias() => []], [$onFieldForSelf->getAlias() => $datum]);
            }
        }

        $leftArray = $tmpArray;
    }

    /**
     * @param array $returnArray
     */
    private function _groupBy(array &$returnArray) {

        $groupedArray = [];

        foreach ($returnArray as $row) {

            $groupKey = [];

            foreach ($this->_group as $field) {


                if (array_key_exists($field->getAlias(), $row)) {

                    $groupKey[] = $field->getAlias();

                    if (array_key_exists($field->getField() ,$row[$field->getAlias()])) {

                        $groupKey[] = $field->getField();
                        $groupKey[] = $row[$field->getAlias()][$field->getField()];
                    }

                }else{
                    $groupKey[] = "null";
                }
            }

            $_groupKey = implode(".", $groupKey);

            if (!array_key_exists($_groupKey,  $groupedArray)) {

                $groupedArray[$_groupKey] = $row;
            }
        }

        $returnArray = array_values($groupedArray);
    }

    /**
     * @param string $alias
     * @throws \Exception
     */
    private function checkAliases(string $alias){
        if(in_array($alias, $this->aliases)){
            throw new \Exception("Dublicate alias");
        }else{
            $this->aliases[] = $alias;
        }
    }

    /**
     * @param string $alias
     * @param bool $noAlias
     * @return Field[] | array
     */
    private function getSelectedFields(string $alias = null, bool $noAlias = false)
    {
        /** @var $selectedFields Field[] */
        $selectedFields = [];

        foreach ($this->fields as $field){
            if ($field->getAlias() == $alias || $alias === null){
                if($noAlias){

                    $selectedFields[] = $field->getField();

                }else{

                    $selectedFields[] = $field;
                }
            }
        }
        return $selectedFields;
    }

    /**
     * @param array $row
     * @return array|object
     */
    private function fieldNormalize(array $row)
    {
        $fillNull = array_fill_keys($this->getSelectedFields(null, true), null);
        $normalizedRow = [];

        foreach ($row as $alias => $subRow){
            foreach ($this->fields as $field){
                if($field->getAlias() == $alias && array_key_exists($field->getField(), $subRow)){
                    $normalizedRow[$field->getField()] = $subRow[$field->getField()];
                }
            }
        }

        if($this->_fetchType == self::FETCH_TYPE_OBJECT){

            $normalizedRow = (object) array_merge($fillNull, $normalizedRow);

        }else{

            $normalizedRow = array_merge($fillNull, $normalizedRow);
        }

        return $normalizedRow;
    }

}
