$(document).ready(function()
{
    check();
    setInterval('check();', 300000)
});
function check()
{
    $.ajax({
        type:'POST',
        url:"sms/check.php",
        data: "process=check",
        dataType: 'json',
        success: function(datax)
        {
            if(datax.typeinfo == "Success")
            {
                send();
            }
            else
            {
            }
        }
    });
}
function send()
{
    $.ajax({
        type:'POST',
        url:"sms/send.php",
        data: "process=send",
        dataType: 'json',
        success: function(datax)
        {
            if(datax.typeinfo == "Success")
            {
                confirm();
            }
            else
            {
            }
        }
    });
}
function confirm()
{
    $.ajax({
        type:'POST',
        url:"sms/confirm.php",
        data: "process=confirm",
        dataType: 'json',
        success: function(datax)
        {
            if(datax.typeinfo == "Success")
            {
                console.log("Ok: Confirmed");
            }
            else
            {
            }
        }
    });
}
