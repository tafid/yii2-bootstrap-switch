<?php
/**
 * Yii2 Bootstrap Switch
 *
 * @link      https://github.com/hiqdev/yii2-bootstrap-switch
 * @package   yii2-bootstrap-switch
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\bootstrap_switch;

use hiqdev\bootstrap_switch\traits\BootstrapSwitchTrait;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Class BootstrapSwitch.
 */
class BootstrapSwitch extends InputWidget
{
    const TYPE_CHECKBOX = 1;
    const TYPE_RADIO = 2;

    use BootstrapSwitchTrait;

    /**
     * @var ActiveRecord
     */
    public $model;

    public function init()
    {
        parent::init();
        if ($this->type === self::TYPE_RADIO) {
            if (empty($this->items) || !is_array($this->items)) {
                throw new InvalidConfigException('"$items" cannot be empty and must be of type array');
            }
            $name = $this->hasModel() ? Html::getInputName($this->model, $this->attribute) : $this->name;
            $this->selector = "input:radio[name=\"$name\"]";
        } else {
            $this->selector = "#{$this->options['id']}";
        }
    }

    public function run()
    {
        $this->registerClientScript();

        switch ($this->type) {
            case self::TYPE_CHECKBOX:
                return $this->renderCheckboxInput();
            case self::TYPE_RADIO:
                return $this->renderRadioInput();
            default:
                throw new InvalidConfigException('Wrong input type');
        }
    }

    protected function renderRadioInput()
    {
        $items = [];
        foreach ($this->items as $key => $item) {
            if (!is_array($item)) {
                $options = $this->options;
                $options['value'] = $key;
                $options['label'] = $item;
                $options['labelOptions'] = $this->labelOptions;
            } else {
                $options = ArrayHelper::getValue($item, 'options', []) + $this->options;
                $options['value'] = ArrayHelper::getValue($item, 'value');
                $options['label'] = ArrayHelper::getValue($item, 'label', false);
                $options['labelOptions'] = ArrayHelper::getValue($item, 'labelOptions', []) + $this->labelOptions;
            }
            if ($this->inline) {
                $options['container'] = '';
            }
            $items[] = $this->hasModel() ? Html::activeRadio($this->model, $this->attribute, $options)
                : Html::radio($this->name, $this->checked, $options);
        }
        $this->containerOptions['class'] = ArrayHelper::getValue($this->containerOptions, 'class', 'form-group');

        return Html::tag('div', implode($this->separator, $items), $this->containerOptions);
    }

    protected function renderCheckboxInput()
    {
        if ($this->hasModel()) {
            $input = Html::activeCheckbox($this->model, $this->attribute, $this->options);
        } else {
            $input = Html::checkbox($this->name, $this->checked, $this->options);
        }

        return $this->inlineLabel ? $input : Html::tag('div', $input);
    }
}
