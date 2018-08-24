$(document).ready(function() {
    var name = "";
    var gender = "";
    var phone = "";
    var job_number = "";
    $("#submit-btn").on("click",()=>{
        if($("#username")!='' &&  $("input:radio[name='sex'][checked]")!='' && $("#mobile")!='' && $("#job_number") !='')
        {
            name = $("#username").val();
            gender = $("input:radio[name = 'sex']:checked").val();
            phone = $("#mobile").val();
            job_number = $("#job_number").val();
            
            $.post("/wechat_zu/public/index.php/api/Technician/add_technician",{
                name:name,
                gender:gender,
                mobile:phone,
                job_number:job_number
            }).done(function(result){
                if(result.status == 1){
                    parent.location.reload()
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.layer.close(index);
                }
            })
        }

    })


})