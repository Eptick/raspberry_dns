$(document).ready(function(){
    $("#approve_ip").click(function(){
        $(this).removeClass("btn-primary");
        $(this).removeClass("btn-success");
        $(this).removeClass("btn-danger");
        $(this).addClass("btn-info");
        $(this).prop('disabled', true);
        $.ajax(
        {
            url: "/approve",
            data: {
                "status" : 200
            },
            success: function(result)
            {
                switch(result["status"]){
                    case 200:
                        $("#approve_ip").removeClass("btn-info");
                        $("#approve_ip").addClass("btn-success");
                        $("#approve_ip").prop('disabled', false);
                        $(".status").html("200");
                        $("#deny_ip").hide();
                    break;
                    default:
                        $("#approve_ip").prop('disabled', false);
                    break;
                }

            },
            error: function(result, status, xhr) 
            {
                $("#approve_ip").removeClass("btn-info");
                $("#approve_ip").addClass("btn-danger");
                $("#approve_ip").prop('disabled', false);
            }
        });
    });
    $("#deny_ip").click(function(){
        $(this).removeClass("btn-primary");
        $(this).removeClass("btn-success");
        $(this).removeClass("btn-danger");
        $(this).addClass("btn-info");
        $(this).prop('disabled', true);
        $.ajax(
        {
            url: "/deny",
            data: {
                "status" : 200
            },
            success: function(result)
            {
                switch(result["status"]){
                    case 200:
                        $("#deny_ip").removeClass("btn-info");
                        $("#deny_ip").addClass("btn-success");
                        $("#deny_ip").prop('disabled', false);
                        $(".status").html("200");
                    break;
                    default:
                        $("#deny_ip").prop('disabled', false);
                    break;
                }

            },
            error: function(result, status, xhr) 
            {
                $("#deny_ip").removeClass("btn-info");
                $("#deny_ip").addClass("btn-danger");
                $("#deny_ip").prop('disabled', false);
            }
        });
    });
});