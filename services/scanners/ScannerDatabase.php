<?php

namespace lajax\translatemanager\services\scanners;

use Yii;
use yii\helpers\Console;
use yii\base\InvalidConfigException;
use lajax\translatemanager\services\Scanner;

/**
 * Detecting existing language elements in database.
 * The connection ids of the scanned databases and the table/field names can be defined in the configuration file of translateManager
 * examples:
 * 
 * ~~~
 * 'tables' => [
 *  [
 *      'connection' => 'db',
 *      'table' => 'language',
 *      'columns' => ['name', 'name_ascii']
 *  ],
 *  [
 *      'connection' => 'db',
 *      'table' => 'category',
 *      'columns' => ['name', 'description']
 *  ]
 * ]
 * ~~~
 * 
 * 
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class ScannerDatabase {

    /**
     * @var array array containing the table ids to process.
     */
    private $_tables;

    /**
     * @var Scanner object containing the detected language elements
     */
    private $_scanner;

    /**
     * @param Scaner $scanner
     */
    public function __construct(Scanner $scanner) {
        $this->_scanner = $scanner;
        $this->_tables = Yii::$app->getModule('translatemanager')->tables;

        if (!empty($this->_tables) && is_array($this->_tables)) {
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
     */
    public function run() {
        $this->_scanner->stdout('Detect DatabaseTable - BEGIN', Console::FG_GREY);
        if (is_array($this->_tables)) {
            foreach ($this->_tables as $tables) {
                $this->_scanningTable($tables);
            }
        }
        
        $this->_scanner->stdout('Detect DatabaseTable - END', Console::FG_GREY);
    }

    /**
     * Scanning database table
     * @param array $tables
     */
    private function _scanningTable($tables) {
        $this->_scanner->stdout('Extracting mesages from ' . $tables['table'] . '.' . implode(',', $tables['columns']), Console::FG_GREEN);
        $query = new \yii\db\Query();
        $data = $query->select($tables['columns'])
                ->from($tables['table'])
                ->createCommand(Yii::$app->$tables['connection'])
                ->queryAll();
        foreach ($data as $columns) {
            $columns = array_map('trim', $columns);
            foreach ($columns as $column) {
                $this->_scanner->addLanguageItem(Scanner::CATEGORY_DATABASE, $column);
            }
        }
    }

}
