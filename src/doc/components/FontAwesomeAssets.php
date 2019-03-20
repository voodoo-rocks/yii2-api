<?php

namespace vr\api\doc\components;

use yii\web\AssetBundle;

/**
 * Class FontAwesomeAssets
 * @package vr\api\doc\components
 */
class FontAwesomeAssets extends AssetBundle
{
  /**
   * @var string
   */
  public $sourcePath = '@vendor/components/font-awesome/';

  /**
   * @var array
   */
  public $css = [
    'css/fontawesome-all.min.css',
  ];

  /**
   * @var array
   */
  public $depends = [];
}
