<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace vr\api\components;

use yii\web\AssetBundle;

/**
 * Class VendorAssets
 * @package vr\api\components
 */
class VendorAssets extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@vendor/';

    /**
     * @var array
     */
    public $js = [
        'components/highlightjs/highlight.pack.min.js'
    ];

    /**
     * @var array
     */
    public $css = [
        'components/highlightjs/styles/default.css'
    ];

    /**
     * @var array
     */
    public $depends = [];
}