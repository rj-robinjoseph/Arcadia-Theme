<?php
/**
 * Block
 */
class Block
{
    /**
     * Block data
     * @var array
     */
    private $data;

    /**
     * Block ID
     * @var string
     */
    private $id;

    /**
     * Visibility timezone
     * @var object
     */
    private $timezone;

    /**
     * Create new block
     * @param  array $data Block fields
     * @return void
     */
    public function __construct($data = [])
    {
        $this->timezone = new DateTimeZone(get_option('timezone_string'));
        $this->data = $data;
    }

    /**
     * Store Block ID
     * @param  string $id ID
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Return Block ID
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Fetch all data
     * @return array
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Layout string
     * @return string
     */
    public function layout()
    {
        return $this->data['acf_fc_layout'];
    }

    /**
     * Check existance of data within block
     * @param  string  $field Field name
     * @return boolean
     */
    private function fieldExists($field)
    {
        return array_key_exists($field, $this->data);
    }

    /**
     * Visibilty status
     * @return string
     */
    public function status()
    {
        return $this->fieldExists('visibility') ? $this->data['visibility'] : 'enable';
    }

    /**
     * Return DateTime object for starting visibility
     * @return object Start Date
     */
    public function visibleFrom()
    {
        return new DateTime($this->data['visible_from'], $this->timezone);
    }

    /**
     * Return DateTime object for ending visibility
     * @return object End Date
     */
    public function visibleUntil()
    {
        return new DateTime($this->data['visible_until'], $this->timezone);
    }

    /**
     * Check block visibility
     * @return boolean
     */
    public function isVisible()
    {
        $now = new DateTime('now', $this->timezone);

        switch ($this->status()) {
            case 'disable':
                return false;
            case 'schedule':
                if (($this->fieldExists('visible_until') && $this->visibleUntil() < $now) ||
                    ($this->fieldExists('visible_from') && $this->visibleFrom() > $now)) {
                    return false;
                }
        }

        return true;
    }

    /**
     * Get unique fields containing class information
     * @return array Matching fields
     */
    public function getFieldClasses()
    {
        $classes = array_filter($this->data, function ($value, $key) {
            return substr($key, 0, 6) === 'class_' && !empty($value) && $value !== 'null';
        }, ARRAY_FILTER_USE_BOTH);

        $classes = array_map(function ($key, $value) {
            return is_bool($value) ? str_replace('_', '-', substr($key, 6)) : $value;
        }, array_keys($classes), $classes);

        $combos = array_filter($classes, function ($value) {
            return is_array($value);
        });

        $classes = array_filter($classes, function ($value) {
            return !is_array($value);
        });

        if (!empty($combos)) {
            $classes = array_merge($classes, ...$combos);
        }

        return $classes;
    }
}
