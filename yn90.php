<?php

require_once 'validation/JobValidation.php';
require_once 'validation/IdValidation.php';
require_once 'validation/NameValidation.php';

class BaseValidation {

    protected $errors = array();

    public function getErrorMessages() {
        return $this->errors;
    }
}

class Registration {
   
    public $confectioneries = array();

    const COUNT = 0;
    const OUTPUT = 1;
    const INPUT = 2;
    const DELETE = 3;
    const CVS = 4;
    const CLOSE = 5;
    const FILE = "./csv/sweets_list.csv";
    const BACKUP = "./csv/sweets_backup.csv";

    public function __construct() {
        if(!is_dir("./csv")) {
            mkdir("./csv", 0777, true);
        }

        if (!file_exists(self::FILE)) {
            $this->printCsvTitle();
        }
    }

    public function printCsvTitle() {
        $fp = fopen(self::FILE, "w+");
        $title = "id, name, JAN-code\n";
        fwrite($fp, $title);
        fclose($fp);
    }

    public function main() {
        echo PHP_EOL . "メインメニュー" . PHP_EOL;
        echo "[1]商品一覧表示" . PHP_EOL;
        echo "[2]商品登録" . PHP_EOL;
        echo "[3]商品削除" . PHP_EOL;
        echo "[4]商品CSV出力" . PHP_EOL;
        echo "[5]終了" . PHP_EOL;
        echo "番号を入力してリターンキーを押してください。" . PHP_EOL;

        $this->selectMenu();
        $this->main();
        return;
    }

    public function selectMenu() {

        $memo = $this->input('job');

        if($memo == self::OUTPUT) {
            $this->outputProductList();
            return;
        }
        if($memo == self::INPUT) {
            $this->inputProduct();
            return;
        }
        if($memo == self::DELETE) {
            $this->deleteProduct();
        }
        if($memo == self::CVS) {
            $this->printCsv();
            return;
        }
        if($memo == self::CLOSE) {
            $this->close();
            return;
        }
    }

    public function input($type) {

        $memo = trim(fgets(STDIN));

        if($type === 'job') {

            $validation = new JobValid;
            $check = $validation->check($memo);
        }

        if($type === 'id') {

            $validation = new IdValid;
            $check = $validation->check($memo);
        }

        if($type === 'name') {

            $validation = new NameValid;
            $check = $validation->check($memo);
        }
        if($check == false) {
            foreach( $validation->getErrorMessages() as $error ) {
                echo $error . PHP_EOL;
            }
            return $this->input($type);
        }
        return $memo;
    }

    public function outputProductList() {

        echo "商品一覧" . PHP_EOL;        
        $fp1 = fopen(self::FILE, "r");

        while (!feof($fp1)) {
            $text = fgets($fp1);
            if($text == ""){
                return;
            }
            $trimmed = trim($text);
            if($trimmed ==! ""){
                echo $trimmed . PHP_EOL;
            }
        }
        fclose($fp1);
        return;
    }

    public function generateJan($id) {
        //JANコード生成(自動) ※生成ルールは、9桁のランダムな数字 + ID３桁とする
        $unique = rand(100000000,999999999);
        $uniqueId = sprintf("%s%003d", $unique, $id);
        return $uniqueId;
    }

    public function countProduct() {
        $fp1 = fopen(self::FILE, "r");
        for($count = self::COUNT; fgets($fp1); $count++);
        fclose($fp1);
        return $count;
    }

    public function inputProduct() {

        echo "商品名を入力して下さい。" . PHP_EOL;
        $name = $this->input('name');
        $this->name = $name;

        $id = $this->countProduct();

        $uniqueId = $this->generateJan($id);

        $fp2 = fopen(self::FILE, "a+");
        $line = sprintf("%d, %s, %d", $id, $name, $uniqueId);
        fwrite($fp2, $line . PHP_EOL);
        fclose($fp2);

        echo "商品名：{$name}" . PHP_EOL;
        echo "ID:{$id}" . PHP_EOL;
        echo "JANコード:{$uniqueId}" . PHP_EOL;
        echo "商品を登録しました。" . PHP_EOL;
        return;
    }

    public function deleteProduct() {
        echo "削除したい商品のidを入力してください。" . PHP_EOL;
        $id = $this->input('id');
        $confectioneries = file(self::FILE, FILE_IGNORE_NEW_LINES);
        
        echo "id:{$id}を削除します。" . PHP_EOL;

        $fp1 = fopen(self::FILE, "r");
        $fp3 = fopen(self::BACKUP, "w+");

        while (!feof($fp1)) {
            $text = fgets($fp1);
            if(preg_match("/^$id/", $text)) {
                continue;
            }
            /*if($text == ""){
                continue;
            }*/
            $trimmed = trim($text);
            fwrite($fp3, $trimmed . PHP_EOL);
        }
        fclose($fp1);
        fclose($fp3);

        $fp3 = fopen(self::BACKUP, "r");
        $fp1 = fopen(self::FILE, "w+");

        while (!feof($fp3)) {
            $text1 = fgets($fp3);
            if($text1 == ""){
                continue;
            }
            $trimmed1 = trim($text1);
            fwrite($fp1, $trimmed1 . PHP_EOL);
        }
        fclose($fp1);
        fclose($fp3);

        /*
        unset($confectioneries[$id]);

        $this->printCsvTitle();

        $fp2 = fopen(self::FILE, "a+");
        for($id = self::OUTPUT; $id <=count($confectioneries); $id++) {
            $line = "$confectioneries[$id]";
            fwrite($fp2, $line . PHP_EOL);
        }
        fclose($fp2);
*/
        $this->outputProductList();
        return;
    }

    public function printCsv() {
        $now = date('YmdHis');
        $printCsv = "./csv/item_list_{$now}.csv";
   
        $fp1 = fopen(self::FILE, "r");
        $fpCsv2 = fopen($printCsv, "a+");

        while (!feof($fp1)) {
            $text = fgets($fp1);
            if($text == ""){
                return;
            }
            $trimmed = trim($text);
            if($trimmed ==! ""){
                fwrite($fpCsv2, $trimmed . PHP_EOL);
            }
        }
        fclose($fpCsv2);
        fclose($fp1);
    }

    public function close() {
        echo "終了します。" . PHP_EOL;
        exit();
    }
}
$register = new Registration;
$register->main();

?>