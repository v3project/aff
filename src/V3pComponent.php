<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 04.12.2017
 */

namespace v3p\aff;

use skeeks\cms\backend\BackendComponent;
use skeeks\cms\base\Component;
use skeeks\cms\models\CmsContent;
use skeeks\widget\chosen\Chosen;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\Module;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Application;
use yii\widgets\ActiveForm;

/**
 * Class V3pComponent
 * @package v3p\aff
 */
class V3pComponent extends Component
{
    public $key;

    public $cms_content_id;

    /**
     * @return array
     */
    static public function descriptorConfig()
    {
        return array_merge(parent::descriptorConfig(), [
            'name' => 'Настройки V3Project',
        ]);
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['key'], 'string'],
            [['cms_content_id'], 'integer'],
            [['key', 'cms_content_id'], 'required'],
        ]);
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'key' => 'Код аффилиата полученный в V3Project',
            'cms_content_id' => 'Контент cms являющийся товарами V3Project',
        ]);
    }

    public function attributeHints()
    {
        $link = urlencode(Url::base(true));
        $a = Html::a('http://www.seogadget.ru/ip?urls=' . $link, 'http://www.seogadget.ru/ip?urls=' . $link,
            ['target' => '_blank']);

        return ArrayHelper::merge(parent::attributeHints(), [
            'affiliate_key' => 'Ключ связан с ip адресом сайта, необходимо сообщить свой IP. Проверить IP можно тут: ' . $a,
        ]);
    }

    public function renderConfigForm(ActiveForm $form)
    {
        echo $form->fieldSet('Общие настройки');
        echo $form->field($this, 'key');
        echo $form->field($this, 'cms_content_id')->widget(Chosen::className(), [
            'items' => CmsContent::getDataForSelect(),
        ]);
        echo $form->fieldSetEnd();
    }
}