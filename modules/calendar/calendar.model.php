<?php
class calendarModel extends calendar 
{
    function triggerModuleListInSitemap(&$obj){
        array_push($obj,'calendar');
    }
}


?>