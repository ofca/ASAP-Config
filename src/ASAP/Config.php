<?php

namespace ASAP;

/**
 * Lightweight config class.
 *
 * @author ofca <ofca@emve.org>
 */
class Config implements \ArrayAccess
{
    protected $values = array();
    protected $pathes = array();

    /**
     * Instantiate config.
     * Configs can be passed as argument to the constructor.
     * 
     * @param array $values Configs.
     */
    public function __construct($values = array())
    {
        $this->values = $values;
    }

    /**
     * Return config value.
     * This is main method to get config value. Every other way
     * to get config value is just a alias to this method.
     * 
     * @param  string|array $path Dot path or array of dot pathes.
     * @param  mixed $default Default value to return if key not exists.
     * @return mixed Config value
     */
    public function get($path, $default = null)
    {
        // $this->get(array('foo', 'bar'));
        if (is_array($path)) {
            $values = array();

            foreach ($path as $key) {
                $values[$key] = $this->get($key, $default);
            }

            return $values;
        }

        $keys = explode('.', $path);
        $name = $keys[0];

        if ( ! isset($this->values[$name])) {
            $this->load($name);
        }

        if ($keys) {
            $array = $this->values;

            foreach ($keys as $key) {
                if (isset($array[$key])) {
                    $array = $array[$key];
                } else {
                    return $default;
                }
            }

            return $array;
        }
        
        return $default;
    }

    /**
     * Alias for Config::get.
     * 
     * @param  string $key Dot path.
     * @return mixed Config value.
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Add path to the folder where we will look for the config file.
     *
     * Important: This method clears previous loaded values! Every
     * config will be loaded once again.
     * 
     * @param array|string $pathes String or array of strings.
     * @param boolean $last Insert on top of stack so it's low prior.
     * @return $this
     */
    public function addPath($pathes = array(), $onTop = false)
    {
        if ( ! is_array($pathes)) {
            $pathes = array($pathes);
        }

        // Normalize pathes
        foreach ($pathes as $key => $path) {
            $pathes[$key] = rtrim($path, '/').'/';
        }

        if ($onTop) {
            foreach (array_reverse($pathes) as $path) {
                array_unshift($this->pathes, $path);
            }
        } else {
            $this->pathes = array_merge($this->pathes, $pathes);
        }
        
        $this->values = array();

        return $this;
    }

    /**
     * Load configuration files.
     * @param  string $name name of the file (without extension).
     * @return boolean
     */
    public function load($name)
    {
        $arrays = array();

        foreach ($this->pathes as $path) {
            if (is_file($path.$name.'.php')) {
                $arrays[] = include $path.$name.'.php';
            }
        }

        if ( ! $arrays) {
            return false;
        }

        if (count($arrays) > 1) {
            $array = call_user_func_array('array_replace_recursive', $arrays);
        } else {
            $array = $arrays[0];
        }

        $this->values[$name] = $array;

        return true;
    }

    // -- ArrayAccess interface
    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException('Config is immutable.');
    }

    public function offsetUnset($offset)
    {
        throw new \RuntimeException('Config is immutable.');   
    }
}