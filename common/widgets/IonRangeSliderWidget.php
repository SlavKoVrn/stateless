<?php

namespace common\widgets;

use yii\helpers\StringHelper;
use yii\widgets\InputWidget;

class IonRangeSliderWidget extends InputWidget {

    public $type = 'double';
    public $skin = 'big';
    public $grid = true;
    public $id = 'ion_range_slider';
    public $name = 'ion_range_slider';
    public $class = 'ion_range_slider';
    public $min = 0;
    public $max = 10;
    public $from = 2;
    public $to = 7;
    public $button = 'button';
    public $pjax_search = 'search';
    public $pjax_list = 'list';

    protected $path;

    public function init() {
        if($this->hasModel())
        {
            $modelName = StringHelper::basename(get_class($this->model));
            $this->id = strtolower($modelName.'_'.$this->attribute);
            $this->class = strtolower($modelName.'_'.$this->attribute);
            $this->name = $modelName.'['.$this->attribute.']';
        }
        parent::run();
    }

    public function run() {

        parent::run();

        $this->registryScript();

        return $this->render('ionrangeslider',[
            'id' => $this->id,
            'class' => $this->class,
            'name' => $this->name,
            'path' => $this->path,
        ]);
    }

    protected function registryScript()
    {
        $path = \Yii::$app->getAssetManager()->publish(__DIR__ . '/assets/ionrangeslider/');
        $this->path = $path[1];

        $this->getView()->registerCssFile($this->path . '/css/ion.rangeSlider.min.css');
        $this->getView()->registerJsFile(
            $this->path . '/js/ion.rangeSlider.min.js',
            [
                'position' => \yii\web\View::POS_END,
                'depends'  => ['\yii\web\JqueryAsset'],
            ]
        );

        $script =<<<JS
            $('#{$this->pjax_search}').unbind('submit');
            $('#{$this->pjax_search}').on('pjax:success', function (e, xhr, settings){
                $.pjax.reload({'container':'#{$this->pjax_list}','timeout':false,'async':false});
                window.ionslider_loading.hide();
                window.ionslider_button.show();
                window.ionslider_ids.forEach(function(item, i, arr) {
                    item();
                });
              }
            );
            window.ionslider_{$this->id}_min = {$this->min};
            window.ionslider_{$this->id}_max = {$this->max};
            window.ionslider_{$this->id}_from = {$this->from};
            window.ionslider_{$this->id}_to = {$this->to};
            window.ionslider_{$this->id}_start = function(){
                $("#{$this->id}").ionRangeSlider({
                    'type':'{$this->type}',
                    'skin':'{$this->skin}',
                    'grid':{$this->grid},
                    'min':window.ionslider_{$this->id}_min,
                    'max':window.ionslider_{$this->id}_max,
                    'from':window.ionslider_{$this->id}_from,
                    'to':window.ionslider_{$this->id}_to
                });
                window.ionslider_{$this->id} = $('#{$this->id}').data('ionRangeSlider');
                window.ionslider_loading = $("#ionslider-loading-{$this->id}");
                $('#{$this->button}').click(function(e){
                    window.ionslider_loading.show();
                    window.ionslider_button = $('#{$this->button}');
                    window.ionslider_button.hide();
                    window.ionslider_{$this->id}_min = window.ionslider_{$this->id}.result.min;
                    window.ionslider_{$this->id}_max = window.ionslider_{$this->id}.result.max;
                    window.ionslider_{$this->id}_from = window.ionslider_{$this->id}.result.from;
                    window.ionslider_{$this->id}_to = window.ionslider_{$this->id}.result.to;
                });
            };
            window.ionslider_{$this->id}_start();
            if (typeof window.ionslider_ids == 'undefined'){
                window.ionslider_ids = [];
            }
            window.ionslider_ids.push(window.ionslider_{$this->id}_start);
JS;
        $this->getView()->registerJs($script,\yii\web\View::POS_READY);
    }
}