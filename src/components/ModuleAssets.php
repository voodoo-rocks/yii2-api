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
        '//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.7.0/highlight.min.js',
        '//cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js',
        '//cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.13/clipboard.min.js',
        'js/api.js',
    ];

    /**
     * @var array
     */
    public $css = [
        '//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.7.0/styles/default.min.css',
    ];

    /**
     * @var array
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}