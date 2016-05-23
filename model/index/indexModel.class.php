<?php
//index model
class indexModel extends Model{
    
    public function getNewsList() {
        $querySql = "select * from news order by sort desc";
        return $this->con->getAll($querySql);
    }
}
?>