<?php

namespace lajax\translatemanager\services\scanners;

use Yii;
use yii\base\InvalidConfigException;
use lajax\translatemanager\services\Scanner;


/**
 * <pre>Detecting existing language elements in database.
 * The connection ids of the scanned databases and the table/field names can be defined in the configuration file of translateManager
 * examples:
 * 'tables' [
 *  [
 *      'connection' => 'db',
 *      'table' => 'language',
 *      columns => ['name’, ‘name_ascii']
 *  ],
 *  [
 *      'connection' => 'db',
 *      'table' => 'category',
 *      columns => ['name’, ‘description']
 *  ],
 * ]
 * </pre>
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class ScannerDatabase {

    /**
     * @var array array containing the table ids to process.
     */
    private $_tables;

    /**
     * @var array array containing the detected language elements
     */
    private $_languageItems;

    /**
     * @param array $languageItems
     */
    public function __construct($languageItems = []) {
        $this->_languageItems = $languageItems;
        $this->_tables = Yii::$app->getModule('translatemanager')->tables;
        
        if (is_array($this->_tables)) {
            foreach ($this->_tables as $tables) {
                if (empty($tables['connection'])) {
                    throw new InvalidConfigException('Incomplete database  configuration: connection ');
                } else if (empty($tables['table'])) {
                    throw new InvalidConfigException('Incomplete database  configuration: table ');
                } else if (empty($tables['columns'])) {
                    throw new InvalidConfigException('Incomplete database  configuration: columns ');
                }
            }
        }
    }

    /**
     * Scanning database tables defined in configuration file. Searching for language elements yet to be translated.
     * @return array
     */
    public function scanning() {
        if (is_array($this->_tables)) {
            foreach ($this->_tables as $tables) {
                $this->_scanningTable($tables);
            }
        }

        return $this->_languageItems;
    }

    /**
     * Scanning database table
     * @param array $tables
     */
    private function _scanningTable($tables) {
        $data = (new \yii\db\Query())
                ->select($tables['columns'])
                ->from($tables['table'])
                ->createCommand(Yii::$app->$tables['connection'])
                ->queryAll();
        foreach ($data as $columns) {
            $columns = array_map('trim', $columns);
            foreach ($columns as $column) {
                $this->_languageItems[Scanner::CATEGORY_DATABASE][$column] = true;
            }
        }
    }

}
