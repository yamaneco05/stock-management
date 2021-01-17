<?php

class NameValid extends BaseValidation{

    public function check($memo) {

        if($memo === "") {
            $this->errors[] = "入力されていません。\n商品名を入力してください。";
            return false;
        }
        if( !is_string($memo)) {
            $this->errors[] = "商品名を入力してください。";
            return false;
        }
        return $memo;
    }
}

?>