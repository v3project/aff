<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 06.11.2017
 */
$config = [
    'components' => [

        'v3p' => [
            'class' => \v3p\aff\V3pComponent::class,
        ],

        'cmsAgent' => [
            'commands' => [

                'v3p/sync/features' => [
                    'class' => \skeeks\cms\agent\CmsAgent::class,
                    'name' => 'Синхронизация характеристик V3Project',
                    'interval' => 3600 * 2,
                ],
            ],
        ],
    ],

    'modules' => [
        'v3p' => [
            'class' => 'v3p\aff\V3pModule',
        ],
    ],

];


return $config;
