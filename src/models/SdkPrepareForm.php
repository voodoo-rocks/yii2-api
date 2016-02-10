<?php
/**
 * @copyright Copyright (c) 2013-2016 Voodoo Mobile Consulting Group LLC
 * @link      https://voodoo.rocks
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace vm\api\models;

use yii\base\Model;
use yii\db\TableSchema;
use yii\helpers\Inflector;

/**
 * Class SdkPrepareForm
 * @package vm\api\models
 */
class SdkPrepareForm extends Model
{
    /**
     * @var
     */
    public $rootPath;

    /**
     * @var string
     */
    public $modelsPackage = 'com.voodoomobile.app';
    /**
     * @var string
     */
    public $baseClass = 'Entity';

    /**
     * @return bool
     */
    public function generate()
    {
        $this->generateModels();
        $this->generateServices();

        $this->saveEnvironment();

        return true;
    }

    /**
     * @throws \yii\base\NotSupportedException
     */
    private function generateModels()
    {
        $zip           = new \ZipArchive();
        $filename      = sys_get_temp_dir() . Inflector::slug(\Yii::$app->name) . '-models.zip';
        $zip->filename = $filename;
        $zip->open($filename, \ZipArchive::OVERWRITE);
        //        $zip->filename = sys_get_temp_dir() . '/' . Inflector::slug(\Yii::$app->name) . '-models.zip';

        $tables = \Yii::$app->db->getSchema()->getTableSchemas();

        $modelTemplate = file_get_contents(\Yii::getAlias('@api') . '/templates/android.class.java');
        $fieldTemplate = file_get_contents(\Yii::getAlias('@api') . '/templates/android.field.java');

        /** @var TableSchema $table */
        foreach ($tables as $table) {

            $fields    = $this->generateFields($table, $fieldTemplate);
            $className = Inflector::camelize($table->name);
            $output    = str_replace([
                '{package}',
                '{class-name}',
                '{fields}',
            ], [
                $this->modelsPackage,
                Inflector::id2camel($className),
                $fields,
            ], $modelTemplate);

            $zip->addFromString($className . '.java', $output);
        }

        $zip->close();

        \Yii::$app->response->sendFile($filename);
    }

    /**
     * @param TableSchema $table
     * @param string      $template
     *
     * @return string
     */
    private function generateFields($table, $template)
    {
        $output = [];
        foreach ($table->getColumnNames() as $columnName) {
            $output[] = str_replace([
                '{serialized-name}',
                '{type}',
                '{field-name}',
                '{capitalized-field-name}',
            ], [
                $columnName,
                $table->getColumn($columnName)->phpType,
                Inflector::variablize($columnName),
                Inflector::camelize($columnName),
            ], $template);
        }

        return implode("\r\n", $output);
    }

    /**
     *
     */
    private function generateServices()
    {
    }

    /**
     *
     */
    private function saveEnvironment()
    {
        \Yii::$app->session->set('rootPath', $this->rootPath);
        \Yii::$app->session->set('modelsPackage', $this->modelsPackage);
    }

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->restoreEnvironment();
    }

    /**
     *
     */
    private function restoreEnvironment()
    {
        $this->rootPath      = \Yii::$app->session->get('rootPath');
        $this->modelsPackage = \Yii::$app->session->get('modelsPackage');
    }
}