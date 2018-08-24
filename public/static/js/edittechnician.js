$(document).ready(function() {
    var ori_job_number = $.getUrlParam("jbnb");
    var name = "";
    var gender = "";
    var phone = "";
    var job_number = "";
    $("#submit-btn").on("click", () => {
        if ($("#username") != '' && $("input:radio[name='sex'][checked]") != '' && $("#mobile") != '' && $("#job_number") != '') {
            name = $("#username").val();
            gender = $("input:radio[name = 'sex']:checked").val();
            phone = $("#mobile").val();
            job_number = $("#job_number").val();
            
            $.post("./../../api/Technician/update_technician",{
                ori_job_number:ori_job_number,
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