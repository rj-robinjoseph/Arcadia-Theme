<?php
/**
 * Opening Hours
 */
class OpeningHours
{
    /**
     * Return Schema openingHours
     * @param  array $opts Rendering options
     * @return void
     */
    public static function render($opts = [])
    {
        $options = array_merge([
            'echo' => true,
            'display' => 'div',
            'closed' => true,
            'combine' => false,
            'format' => 'g:i a',
            'divider' => ' - ',
            'class' => 'hours',
            'between' => ': ',
            'length' => null,
        ], $opts);

        switch ($options['combine']) {
            case 'all':
                $hours = StoreHours::combineAll();
                break;
            case 'linear':
                $hours = StoreHours::combineLinear();
                break;
            default:
                $hours = StoreHours::data();
                break;
        }

        if ($options['display'] == 'table') {
            $options['between'] = '</td><td>';
        }

        $html = self::startHours($options);

        foreach ($hours as $data) {
            $labels = $data['label'];
            $lbl = $data['label'];

            if (is_array($labels)) {
                $labels = array_map(function ($label) use ($options) {
                    return is_null($options['length']) ? $label : substr($label, 0, $options['length']);
                }, $labels);

                $lbl = array_map(function ($label) {
                    return substr($label, 0, 2);
                }, $lbl);

                $lbl = implode(',', $lbl);
            } else {
                $labels = is_null($options['length']) ? $labels : substr($labels, 0, $options['length']);
                $lbl = substr($lbl, 0, 2);
            }

            if ($options['combine'] == 'all') {
                $labels = implode(', ', $labels);
            }

            if ($options['combine'] == 'linear') {
                $labels = count($labels) > 1 ? $labels[0] . $options['divider'] . $labels[count($labels) - 1] : implode(', ', $labels);
            }

            if ($data['closed']) {
                if ($options['closed']) {
                    $html .= self::beforeHour($options) . '>';
                    $html .= $labels . $options['between'] . __('Closed', 'schema');
                    $html .= self::afterHour($options);
                }
            } else {
                $html .= self::beforeHour($options) . ' itemprop="openingHours" content="' . $lbl . ' ' . $data['open'] . '-' . $data['close'] . '">' . $labels . $options['between'] . date($options['format'], strtotime($data['open'])) . $options['divider'] . date($options['format'], strtotime($data['close'])) . self::afterHour($options);
            }
        }

        $html .= self::endHours($options);

        if (!$options['echo']) {
            return $html;
        }

        echo $html;
    }

    /**
     * HTML to be displayed before individual entry
     * @param  array  $options Display options
     * @return string          HTML
     */
    private static function beforeHour($options)
    {
        switch ($options['display']) {
            case 'list':
                return '<li';
            case 'table':
                return '<tr><td';
            case 'div':
                return '<div';
        }
    }

    /**
     * HTML to be displayed after individual entry
     * @param  array  $options Display options
     * @return string          HTML
     */
    private static function afterHour($options)
    {
        switch ($options['display']) {
            case 'list':
                return '</li>';
            case 'table':
                return '</td></tr>';
            case 'div':
                return '</div>';
        }
    }

    /**
     * HTML to be displayed before hours output
     * @param  array  $options Display options
     * @return string          HTML
     */
    private static function startHours($options)
    {
        switch ($options['display']) {
            case 'list':
                return '<ul class="' . $options['class'] . '">';
            case 'table':
                return '<table class="' . $options['class'] . '">';
            case 'div':
                return '<div class="' . $options['class'] . '">';
        }
    }

    /**
     * HTML to be displayed after hours output
     * @param  array  $options Display options
     * @return string          HTML
     */
    private static function endHours($options)
    {
        switch ($options['display']) {
            case 'list':
                return '</ul>';
            case 'table':
                return '</table>';
            case 'div':
                return '</div>';
        }
    }
}
