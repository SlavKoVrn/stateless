<?php
namespace common\rbac\rules;

use yii\base\InvalidCallException;
use yii\rbac\Rule;

class ProductAuthorRule extends Rule
{
    public $name = 'productAuthor';

    public function execute($userId, $item, $params)
    {
        if (empty($params['product'])){
            throw new InvalidCallException('Specify product.');
        }
        return $params['product']->user_id == $userId;
    }
}