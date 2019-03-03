<?php
 
    class calendarAdminModel extends calendar {
        /**
         * @brief 초기화
         **/
        function init() {
        }
 
        function getCalendarAdminList($args){
            $output = executeQueryArray('calendar.getCalendarList', $args);

            return $output;
        }


   }
?>