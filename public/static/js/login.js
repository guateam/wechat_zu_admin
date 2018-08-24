$(document).ready(function() {
    var password = $("#password");
    var username = $("#username");
    var login = $("#login-button");
    //var captcha = false
    //jigsaw.init(document.getElementById('captcha'), function() {
    //    captcha = true
    //},3000)
    $('.refreshIcon').addClass('text-right')
    login.on("click", function() {
        if (password.val() == '') {
            swal("错误", "密码不能为空", "error");
            error_password();
        } else pass_password();
        if (username.val() == '') {
            swal("错误", "用户名不能为空", "error");
            error_username();
        } else pass_username();
        
        if (password.val() != '' && username.val() != '') {
                $.post("../public/index.php/api/admin/login", {
                    username: username.val(),
                    password: password.val()
                }).done(function(result) {
                    if (result.status === 1) {
                        swal("成功", "登录成功!", "success").then((ok) => {
                            window.location.href = "../public/index.php/admin/index"
                        })
                    } else if (result.status === 0) {
                        swal("权限不足", "您不是管理员!", "error");
                    } else if (result.status === -1) {
                        swal("错误", "密码不正确", "error");
                        error_username();
                    }else if (result.status === -2) {
                        swal("错误", "用户名不存在", "error");
                        error_username();
                    }
                });
            }

    })
});

function error_username() {
    $("#form-username").addClass("has-error");
}

function pass_username() {
    $("#form-username").removeClass("hass-error");
}

function error_password() {
    $("#form-password").addClass("has-error");
}

function pass_password() {
    $("#form-password").removeClass("has-error");
}