<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace vr\api\components;

use yii\web\AssetBundle;

/**
 * Class ModuleAssets
 * @package vr\api\components
 */
class ModuleAssets extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@api/assets';

    /**
     * @var array
     */
    public $js = [
        'js/api.js',
    ];

    /**
     * @var array
     */
    public $css = [
        'css/api.css',
    ];

    /**
     * @var array
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}