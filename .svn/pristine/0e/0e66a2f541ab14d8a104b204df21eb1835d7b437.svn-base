function drop_confirm(msg, url)
{
   	if(confirm(msg))
   	{
   		window.location = url;
	}
}

function set_val(id,val)
{
	$("#"+id).val(val) ;
}

function chk_all()
{
	if($("#all").prop("checked"))
		$(".check-all").prop("checked",true);
	else
		$(".check-all").prop("checked",false);
}

function form_submit(id,action,msg)
{
	if(msg != "" && msg != null)
	{
		if(confirm(msg))
	   	{
			$("#"+id).attr("action",action) ;
			$("#"+id).submit() ;
		}
	}
	else
	{
		$("#"+id).attr("action",action) ;
		$("#"+id).submit() ;
	}
}