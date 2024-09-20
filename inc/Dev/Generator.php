<?php

namespace Arcadia\Dev;

/**
 * Individual field generation
 */
class Generator
{
    public $content = '';
    public $fields;
    public $indent;
    public $name;
    public $type = 'component';

    private $partials = [
        'group_59fbc9fa02cfe' => 'title',
        'group_57b47f2ad4ee8' => 'buttons',
        'group_58822f9c56d26' => 'button',
        'group_59d2f8315cf01' => 'link',
    ];

    public function __construct($fields = [], $indent = 1)
    {
        $this->fields = $fields;
        $this->indent = $indent;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;

        if ($type === 'block') {
            $this->indent += 1;
        }

        return $this;
    }

    public function is($type)
    {
        return $this->type === $type;
    }

    public function text($field)
    {
        return $this->padding() . '<?php Field::html(\'' . $field['name'] . '\'); ?>' . PHP_EOL;
    }

    public function textarea($field)
    {
        return $this->padding() . '<?php Field::html(\'' . $field['name'] . '\'); ?>' . PHP_EOL;
    }

    public function wysiwyg($field)
    {
        return $this->padding() . '<?php Field::display(\'' . $field['name'] . '\'); ?>' . PHP_EOL;
    }

    public function image($field)
    {
        $key = $field['name'];

        if ($field['return_format'] === 'array') {
            $key .= '.ID';
        }

        return $this->padding() . '<?php Field::image(\'' . $key . '\', \'large\'); ?>' . PHP_EOL;
    }

    public function repeater($field)
    {
        $content = '';

        $generator = new Generator($field['sub_fields'], $this->indent + 2);

        $content .= $this->padding() . '<?php foreach (Field::iterable(\'' . $field['name'] . '\') as $item) : ?>' . PHP_EOL;
        $content .= $this->padding(1) . '<div class="">' . PHP_EOL;
        $content .= $generator->output();
        $content .= $this->padding(1) . '</div>' . PHP_EOL;
        $content .= $this->padding() . '<?php endforeach; ?>' . PHP_EOL;

        return $content;
    }

    public function relationship($field)
    {
        $content = '';

        $content .= $this->padding() . '<?php foreach (Field::relationship(\'' . $field['name'] . '\') as $related) : ?>' . PHP_EOL;
        $content .= $this->padding(1) . '<div class="">' . PHP_EOL;
        $content .= $this->padding(2) . '<?php the_title(); ?>' . PHP_EOL;
        $content .= $this->padding(1) . '</div>' . PHP_EOL;
        $content .= $this->padding() . '<?php endforeach; ?>' . PHP_EOL;

        return $content;
    }

    public function flexible_content($field)
    {
        return $this->padding() . '<?php Layout::flexible(Field::get(\'' . $field['name'] . '\', []), \'blocks\'); ?>' . PHP_EOL;
    }

    public function clone($field)
    {
        if ($field['clone'][0] === 'group_5c903f684a8ae') {
            return $this->componentPartial($field);
        }

        if (!array_key_exists($field['clone'][0], $this->partials) || $field['display'] !== 'seamless') {
            return '';
        }

        return $this->padding() . '<?php Layout::partial(\'' . $this->partials[$field['clone'][0]] . '\'); ?>' . PHP_EOL;
    }

    public function componentPartial($field)
    {
        $key = 'content';

        if ($field['display'] !== 'seamless') {
            $key = $field['name'] . '.' . $key;
        }

        return $this->padding() . '<?php Layout::flexible(Field::get(\'' . $key . '\', []), \'components\'); ?>' . PHP_EOL;
    }

    public function wrapperStart()
    {
        $content = '';

        if (!$this->name) {
            return $content;
        }

        if ($this->hasBackground()) {
            $content .= '<div class="<?php Layout::classes(\'' . $this->name . '\'); ?>" style="<?php Layout::partial(\'background\'); ?>"<?php Layout::id(); ?>>' . PHP_EOL;
            $content .= $this->padding(-1) . '<?php Layout::partials(\'videobg\', \'overlay\'); ?>' . PHP_EOL;
        } else {
            $content .= '<div class="<?php Layout::classes(\'' . $this->name . '\'); ?>"<?php Layout::id(); ?>>' . PHP_EOL;
        }

        if ($this->is('block')) {
            $content .= $this->padding(-1) . '<div class="container">' . PHP_EOL;
        }

        return $content;
    }

    public function wrapperEnd()
    {
        $content = '';

        if (!$this->name) {
            return $content;
        }

        if ($this->is('block')) {
            $content .= $this->padding(-1) . '</div>' . PHP_EOL;
        }

        $content .= '</div>' . PHP_EOL;

        return $content;
    }

    public function blank()
    {
        return '<!-- Code -->' . PHP_EOL;
    }

    public function padding($increment = 0)
    {
        return str_pad('', ($this->indent + $increment) * 4);
    }

    public function hasBackground()
    {
        $matches = array_filter($this->fields, function ($field) {
            return $field['type'] === 'clone' && $field['clone'][0] === 'group_57b4b470e9d29';
        });

        return !empty($matches);
    }

    public function output()
    {
        foreach ($this->fields as $field) {
            if (!method_exists($this, $field['type'])) {
                continue;
            }

            $this->content .= call_user_func([$this, $field['type']], $field);
        }

        if (empty($this->content)) {
            $this->content = $this->blank();
        }

        return $this->wrapperStart() . $this->content . $this->wrapperEnd();
    }
}
