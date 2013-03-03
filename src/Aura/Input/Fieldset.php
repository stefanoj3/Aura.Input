<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Input
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Input;

use ArrayObject;

/**
 * 
 * A fieldset of inputs, where the inputs themselves may be values, other
 * fieldsets, or other collections.
 * 
 * @package Aura.Input
 * 
 */
class Fieldset extends AbstractInput
{
    /**
     * 
     * A builder to create input objects.
     * 
     * @var Builder
     * 
     */
    protected $builder;
    
    /**
     * 
     * Filter for the values.
     * 
     * @var FilterInterface
     * 
     */
    protected $filter;
    
    /**
     * 
     * Inputs in the form.
     * 
     * @var array
     * 
     */
    protected $inputs = [];
    
    /**
     * 
     * Object for retaining information about options available to the form
     * inputs.
     * 
     * @var Options
     * 
     */
    protected $options;
    
    /**
     * 
     * Constructor.
     * 
     * @param BuilderInterface $builder An object to build input objects.
     * 
     * @param FilterInterface $filter A filter object for this fieldset.
     * 
     * @param Options $options An object the hold options for inputs.
     * 
     */
    public function __construct(
        BuilderInterface $builder,
        FilterInterface  $filter,
        Options          $options
    ) {
        $this->builder  = $builder;
        $this->filter   = $filter;
        $this->options  = $options;
        $this->init();
    }
    
    /**
     * 
     * Gets an input value from this fieldset.
     * 
     * @param string $key The input name.
     * 
     * @return mixed The input value.
     * 
     */
    public function __get($key)
    {
        return $this->inputs[$key]->read();
    }
    
    /**
     * 
     * Sets an input value on this fieldset.
     * 
     * @param string $key The input name.
     * 
     * @param mixed $val The input value.
     * 
     * @return void
     * 
     */
    public function __set($key, $val)
    {
        $this->inputs[$key]->load($val);
    }
    
    public function getFilter()
    {
        return $this->filter;
    }
    
    public function getInputs()
    {
        return $this->inputs;
    }
    
    public function getInput($name)
    {
        $input = $this->inputs[$name];
        $input->setArrayName($this->name);
        return $input;
    }
    
    public function getInputNames()
    {
        return array_keys($this->inputs);
    }
    
    public function getBuilder()
    {
        return $this->builder;
    }
    
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * 
     * Loads this fieldset with input values.
     * 
     * @param array $data The values for this fieldset.
     * 
     * @return void
     * 
     */
    public function load($data)
    {
        $data = (array) $data;
        foreach ($this->inputs as $key => $input) {
            if (array_key_exists($key, $data)) {
                $input->load($data[$key]);
            }
        }
        return true;
    }
    
    /**
     * 
     * Reads this fieldset for Fieldset::__get().
     * 
     * @return self
     * 
     * @see Fieldset::__get().
     * 
     */
    public function read()
    {
        return $this;
    }
    
    /**
     * 
     * Exports this fieldset for Fieldset::get().
     * 
     * @return array The array of input objects.
     * 
     * @see Fieldset::get().
     * 
     */
    public function export()
    {
        foreach ($this->inputs as $name => $input) {
            $input->setArrayName($this->name);
        }
        return $this->inputs;
    }
    
    /**
     * 
     * Initializes the inputs and filter.
     * 
     * @return void
     * 
     */
    public function init()
    {
    }
    
    /**
     * 
     * Sets a new Field input.
     * 
     * @param string $name The Field name.
     * 
     * @param string $type A Field of this type; defaults to 'text'.
     * 
     * @return Field
     * 
     */
    public function setField($name, $type = null)
    {
        if (! $type) {
            $type = 'text';
        }
        $this->inputs[$name] = $this->builder->newField($type, $name, $this->name);
        return $this->inputs[$name];
    }
    
    /**
     * 
     * Sets a new Fieldset input.
     * 
     * @param string $name The Fieldset name.
     * 
     * @param string $type A Fieldset of this type; defaults to $name.
     * 
     * @return Fieldset
     * 
     */
    public function setFieldset($name, $type = null)
    {
        if (! $type) {
            $type = $name;
        }
        $this->inputs[$name] = $this->builder->newFieldset($type, $name, $this->name);
        return $this->inputs[$name];
    }
    
    /**
     * 
     * Sets a new Collection input.
     * 
     * @param string $name The Collection name.
     * 
     * @param string $type A Collection of this type of Fieldset; defaults to
     * $name.
     * 
     * @return Collection
     * 
     */
    public function setCollection($name, $type = null)
    {
        if (! $type) {
            $type = $name;
        }
        $this->inputs[$name] = $this->builder->newCollection($type, $name, $this->name);
        return $this->inputs[$name];
    }
    
    /**
     * 
     * Returns an input in a format suitable for a view, generally an array.
     * 
     * @param string $name The input name.
     * 
     * @return mixed
     * 
     */
    public function get($name)
    {
        return $this->getInput($name)->export();
    }
    
    /**
     * 
     * Filters the inputs on this fieldset.
     * 
     * @return bool True if all the filter rules pass, false if not.
     * 
     */
    public function filter()
    {
        return $this->filter->values($this);
    }
    
    /**
     * 
     * Gets the filter messages.
     * 
     * @param string $name The input name to get the filter message for; if
     * empty, gets all messages for all inputs.
     * 
     * @return array The filter messages.
     * 
     */
    public function getMessages($field = null)
    {
        return $this->filter->getMessages($field);
    }
}
