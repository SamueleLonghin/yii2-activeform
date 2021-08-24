<?php


namespace samuelelonghin\form;


use kartik\select2\Select2;
use samuelelonghin\form\RoundSwitchInput;
use Yii;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

class ActiveField extends \yii\bootstrap4\ActiveField
{
    public function init()
    {
        parent::init();
        $this->options['class'] = 'form-group';
    }

    public function placeholder($value)
    {
        $this->inputOptions['placeholder'] = $value;
        return $this;
    }

    public function numberInput(): ActiveField
    {
        return $this->input('number');
    }

    public function dateInput(): ActiveField
    {
        $attribute = $this->attribute;
        return $this->input('datetime-local', ['value' => $this->model->$attribute ? date_create($this->model->$attribute)->format(Yii::$app->params['datetime-local']) : '', 'theme' => Select2::THEME_DEFAULT]);
    }

    public function timeInput(): ActiveField
    {
        $attribute = $this->attribute;
        return $this->input('time', ['value' => $this->model->$attribute ? Yii::$app->formatter->asTime($this->model->$attribute) : '00:00']);
    }

    public function select2Input($items, $options = []): ActiveField
    {
        return $this->widget(Select2::class, array_merge_recursive(['data' => $items], $options));
    }

    public function swtchtInput($options = [])
    {
        return $this->widget(RoundSwitchInput::class, $options);
    }

    public function visible($value)
    {
        if (!$value) {
            $this->template = '';
        }
        return $this;
    }

    public function checkboxList($items, $options = [])
    {
        if (!isset($options['item'])) {
            $this->template = str_replace("\n{error}", '', $this->template);
            $itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : [];
            $encode = ArrayHelper::getValue($options, 'encode', true);
            $itemCount = count($items) - 1;
            $error = $this->error()->parts['{error}'];
            $options['item'] = function ($i, $label, $name, $checked, $value) use (
                $itemOptions,
                $encode,
                $itemCount,
                $error
            ) {
                $options = array_merge($this->checkOptions, [
                    'label' => $encode ? Html::encode($label) : $label,
                    'value' => $value
                ], $itemOptions);
                $wrapperOptions = ArrayHelper::remove($options, 'wrapperOptions', ['class' => ['custom-control', 'custom-checkbox']]);
                if ($this->inline) {
                    Html::addCssClass($wrapperOptions, 'custom-control-inline');
                }

//                $this->addErrorClassIfNeeded($options);
                $html = Html::beginTag('div', $wrapperOptions) . "\n" .
                    Html::checkbox($name, $checked, $options) . "\n";
                if ($itemCount === $i) {
                    $html .= $error . "\n";
                }
                $html .= Html::endTag('div') . "\n";

                return $html;
            };
        }
        return parent::checkboxList($items, $options);
    }
}