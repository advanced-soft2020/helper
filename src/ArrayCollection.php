<?php


namespace Advance\Helper;


use Symfony\Component\PropertyAccess\PropertyAccessor;

class ArrayCollection implements \Iterator
{

    protected $elements = [];

    protected $position;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;
    /**
     * @var string|null
     */
    private $objectClass;

    public function __construct(array $array = [], ?string $objectClass = null)
    {
        $this->position = 0;
        $this->elements = $array;
        $this->objectClass = $objectClass;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @return mixed
     * @throws CollectionItemHasInvalidDataTypeException
     */
    public function current()
    {
        $element = $this->elements[$this->position];
        $this->checkElementType($element);
        return $this->elements[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->elements[$this->position]);
    }

    /**
     * @param $element
     * @throws CollectionItemHasInvalidDataTypeException
     */
    public function add($element)
    {
         $this->checkElementType($element);
         $this->elements[] = $element;
    }

    /**
     * @return array|null
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * @param $entity
     * @return mixed
     */
    public function exist($entity)
    {
        $key = array_search($entity, $this->elements, true);
        if (false === $key) {
            return false;
        }
        return true;
    }

    /**
     * @param $entity
     * @return bool
     */
    public function remove($entity): bool
    {
        $key = array_search($entity, $this->elements, true);
        if (false === $key) {
            return false;
        }
        unset($this->elements[$key]);
        $this->elements = array_values($this->elements);
        return true;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * @param string $field
     * @return array
     */
    public function index(string $field): array
    {
        $pa = $this->getPropertyAccessor();
        foreach ($this as $element){
            $fieldValue = $pa->getValue($element, $field);
            $res[$fieldValue] = $element;
        }
        return  $res ?? [];
    }

    /**
     * @param callable $callback
     * @return array
     */
    public function indexByCallback(callable $callback): array
    {
        foreach ($this as $element){
            $fieldValue = call_user_func_array($callback, [$element]);
            $result[$fieldValue] = $element;
        }
        return $result ?? [];
    }


    /**
     * @param string $field
     * @return self[]
     * @throws CollectionItemHasInvalidDataTypeException
     */
    public function indexCollection(string $field): array
    {
        $pa = $this->getPropertyAccessor();
        foreach ($this as $element){
            $fieldValue = $pa->getValue($element, $field);
            if (!isset($res[$fieldValue])){
                $res[$fieldValue] = $this->createPrototype();
            }
            $res[$fieldValue]->add($element);
        }
        return  $res ?? [];
    }

    /**
     * @param callable $callback
     * @return array
     * @throws CollectionItemHasInvalidDataTypeException
     */
    public function indexByCallbackCollection(callable $callback)
    {

        foreach ($this as $element){
            $fieldValue = call_user_func_array($callback, [$element]);
            if (!isset($res[$fieldValue])){
                $res[$fieldValue] = $this->createPrototype();
            }
            $res[$fieldValue]->add($element);
        }
        return  $res ?? [];
    }

    /**
     * @return $this
     */
    public function createPrototype(): self
    {
        return new self([], $this->objectClass);
    }

    /**
     * @return PropertyAccessor
     */
    protected function getPropertyAccessor(): PropertyAccessor
    {
        if (null === $this->propertyAccessor){
            $this->propertyAccessor = new PropertyAccessor();
        }
        return $this->propertyAccessor;
    }

    /**
     * @param $element
     * @throws CollectionItemHasInvalidDataTypeException
     */
    private function checkElementType($element): void
    {

        if (null !== $this->objectClass && !$element instanceof $this->objectClass){
            throw new CollectionItemHasInvalidDataTypeException($this->objectClass);
        }
    }

}
