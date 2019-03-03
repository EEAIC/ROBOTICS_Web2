<?php
    class calendarAdminController extends calendar 
    {
        function init() 
        {

        }
        function procCalendarAdminInsert($args= null) 
        {
            //request 값을 모두 받음
            $obj = Context::getRequestVars();

            
            $output = executeQuery("calendar.insertCalendar", $obj);

            $this->add('output', $output);

            $this->setMessage('success_registed');

        }

        function procScheduleAdminUpdate($arge = null) 
        {
            //request 값을 모두 받음
            $obj = Context::getRequestVars();
        
            $output = executeQuery("calendar.updateSchedule", $obj);

            $this->add('output', $output);

            $this->setMessage('success_registed');


        }
        function procScheduleAdminDelete($arge = null) 
        {
            //request 값을 모두 받음
            $obj = Context::getRequestVars();
        
            $output = executeQuery("calendar.deleteSchedule", $obj);

            $this->add('output', $output);

            $this->setMessage('success_registed');


        }

        
     
    }
?>