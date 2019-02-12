jQuery(function($){
	//_alert = alert;
	try {
		window.alert = function(arguments){
      return swal({
        text: arguments,
        icon: "warning",
        button: "{$lang->cmd_confirm}"
      });
		};
	}catch(e){}
});
