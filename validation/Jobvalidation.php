<?php

class JobValid extends BaseValidation{

    public function check($memo) {

        if($memo === "") {
            $this->errors[] =  "入力されていません。\n1-5の番号を入力してください。";
            return false;
        }
        if( !ctype_digit($memo) || $memo > Registration::CLOSE ) {
            $this->errors[] = "1-5の番号を入力してください。";
            return false;
        }
        return $memo;
    }
}

?>