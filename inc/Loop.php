<?php
/**
 * Loop Helper
 */
class Loop
{
    /**
     * Current iteration
     * @var integer
     */
    public static $index = 0;

    /**
     * Total iterations
     * @var integer
     */
    public static $count = 1;

    /**
     * History
     * @var array
     */
    private static $history = [];

    /**
     * Set up loop counter
     * @param  integer $count Total number of iterations
     * @return void
     */
    public static function init($count)
    {
        self::$history[] = [
            'index' => self::$index,
            'count' => self::$count,
        ];

        self::$index = 0;
        self::$count = $count;
    }

    /**
     * Restore previous data set
     * @return void
     */
    public static function restore()
    {
        $data = array_pop(self::$history);

        self::$index = $data['index'];
        self::$count = $data['count'];
    }

    /**
     * Bump index by 1
     * @return void
     */
    public static function iterate()
    {
        self::$index += 1;
    }

    /**
     * Determine first iteration
     * @return boolean
     */
    public static function first()
    {
        return self::$index === 0;
    }

    /**
     * Determine last iteration
     * @return boolean
     */
    public static function last()
    {
        return self::$index === self::$count - 1;
    }

    /**
     * Return current index
     * @return integer
     */
    public static function index()
    {
        return self::$index;
    }

    /**
     * Return current iteration
     * @return integer
     */
    public static function iteration()
    {
        return self::$index + 1;
    }

    /**
     * Check even iterations
     * @return boolean
     */
    public static function even()
    {
        return self::iteration() % 2 === 0;
    }

    /**
     * Check odd iterations
     * @return boolean
     */
    public static function odd()
    {
        return self::iteration() % 2 === 1;
    }
}
