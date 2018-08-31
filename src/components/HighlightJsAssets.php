<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace vr\api\components;

use yii\web\AssetBundle;

/**
 * Class HighlightJsAssets
 * @package vr\api\components
 */
class HighlightJsAssets extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@vendor/components/highlightjs/';

    /**
     * @var array
     */
    public $js = [
        'highlight.pack.min.js',
    ];

    /**
     * @var array
     */
    public $css = [
        'styles/default.css',
    ];

    /**
     * @var array
     */
    public $depends = [];
}