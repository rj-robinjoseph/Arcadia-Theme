<?php
/**
 * Store Hours
 */
class StoreHours
{
    /**
     * Days of Week
     * @var array
     */
    private static $days_of_week = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    /**
     * Raw Data
     * @var array
     */
    private static $days = [];

    /**
     * Timezone
     * @var object
     */
    private static $timezone = null;

    /**
     * Raw Data
     * @return array Data
     */
    public static function data()
    {
        self::buildData();

        return self::$days;
    }

    /**
     * Return Day of Week
     * @param  integer $day    Day of Week
     * @param  integer $length String Length
     * @return string          Day of Week
     */
    public static function dayOfWeek($day = null, $length = 999)
    {
        if (!array_key_exists($day, self::$days_of_week)) {
            $day = date('w');
        }

        return substr(self::$days_of_week[$day], 0, $length);
    }

    /**
     * Today's hours
     * @return array Hours
     */
    public static function today()
    {
        $days = self::data();
        $today = date('w');

        return $days[$today];
    }

    /**
     * Open Today
     * @return boolean
     */
    public static function isOpen()
    {
        $today = self::today();
        return !$today['closed'];
    }

    /**
     * Open Now
     * @return boolean
     */
    public static function isOpenNow()
    {
        if (!self::isOpen()) {
            return false;
        }

        $now = new DateTime('now', self::$timezone);
        $today = self::today();
        $currentTime = intval($now->format('Hi'));
        $start = intval(str_replace(':', '', $today['open']));
        $close = intval(str_replace(':', '', $today['close']));

        return $currentTime >= $start && $currentTime < $close;
    }

    /**
     * Store closing soon
     * @param  integer $threshold Soon in minutes
     * @return boolean
     */
    public static function isClosingSoon($threshold = 60)
    {
        if (!self::isOpen()) {
            return false;
        }

        $now = new DateTime('now', self::$timezone);
        $today = self::today();
        $currentTime = intval($now->format('G')) * 60 + intval($now->format('i'));
        list($hour, $min) = explode(':', $today['close']);
        $close = intval($hour) * 60 + intval($min);

        return $close - $currentTime < $threshold && $close - $currentTime > 0;
    }

    /**
     * Check if hours are set
     * @return boolean
     */
    public static function isVisible()
    {
        return get_field('hours', 'option') ? true : false;
    }

    /**
     * Combine days with similar hours
     * @return array
     */
    public static function combineAll()
    {
        $hours = self::data();
        $new_hours = [];

        foreach ($hours as $day => $hour) {
            $key = $hour['open'].$hour['close'].$hour['closed'];

            if (array_key_exists($key, $new_hours)) {
                $new_hours[$key]['label'][] = $hour['label'];
            } else {
                $new_hours[$key] = [
                    'open' => $hour['open'],
                    'close' => $hour['close'],
                    'closed' => $hour['closed'],
                    'label' => [$hour['label']]
                ];
            }
        }

        return array_values($new_hours);
    }

    /**
     * Combine days with similar hours
     * @return array
     */
    public static function combineLinear()
    {
        $hours = self::data();
        $new_hours = [];
        $previous = null;

        foreach ($hours as $day => $hour) {
            $key = $hour['open'].$hour['close'].$hour['closed'];

            if ($key == $previous) {
                $new_hours[count($new_hours) - 1]['label'][] = $hour['label'];
            } else {
                $new_hours[] = [
                    'open' => $hour['open'],
                    'close' => $hour['close'],
                    'closed' => $hour['closed'],
                    'label' => [$hour['label']]
                ];
            }

            $previous = $key;
        }

        return array_values($new_hours);
    }

    /**
     * Build base data set
     * @return void
     */
    private static function buildData()
    {
        if (!empty(self::$days)) {
            return;
        }

        self::$timezone = new DateTimeZone(get_option('timezone_string'));

        $hours = get_field('hours', 'option');
        $overrides = get_field('seasonal_hours', 'option');
        $today = new DateTime();

        // Pre fill days
        for ($i = 0; $i <= 6; $i++) {
            self::$days[$i] = [
                'open' => null,
                'close' => null,
                'closed' => true,
                'label' => self::dayOfWeek($i)
            ];
        }

        // Set Regular Hours
        if ($hours) {
            foreach ($hours as $hour) {
                foreach ($hour['days'] as $day) {
                    self::$days[$day]['open'] = $hour['opening'];
                    self::$days[$day]['close'] = $hour['closing'];
                    self::$days[$day]['closed'] = false;
                }
            }
        }

        // Set Seasonal Hours
        if ($overrides) {
            foreach ($overrides as $over) {
                $from = new DateTime($over['valid_from']);
                $through = new DateTime($over['valid_through']);

                if ($from <= $today && $through >= $today) {
                    foreach ($over['days'] as $day) {
                        self::$days[$day]['open'] = $over['opening'];
                        self::$days[$day]['close'] = $over['closing'];
                        self::$days[$day]['closed'] = $over['closed'] ? true : false;
                    }
                }
            }
        }
    }
}
