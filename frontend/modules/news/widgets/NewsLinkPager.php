<?php
namespace frontend\modules\news\widgets;

use yii\helpers\Html;

class NewsLinkPager extends \yii\widgets\LinkPager
{
    /**
     * Renders a page button.
     * You may override this method to customize the generation of page buttons.
     * @param string $label the text label for the button
     * @param int $page the page number
     * @param string $class the CSS class for the page button.
     * @param bool $disabled whether this page button is disabled
     * @param bool $active whether this page button is active
     * @return string the rendering result
     */
    protected function renderPageButton($label, $page, $class, $disabled, $active)
    {
        if ($disabled) {
            return Html::tag('li', Html::a($label, '#',['onclick' => 'return false;']));
        }
        return Html::tag('li', Html::a($label, $this->pagination->createUrl($page),
            ['class' => (($active)?'active':'')]
        ));
    }

}
