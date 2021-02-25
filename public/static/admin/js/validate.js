// 验证手机号码
function vailPhone(phone){
    var message = "";
    var myreg = /^(13[0-9]{9})|(18[0-9]{9})|(14[0-9]{9})|(17[0-9]{9})|(15[0-9]{9})$/;
    if(phone == ''){
        message = "手机号码不能为空";
    }else if(phone.length !=11){
        message = "请输入有效的手机号码";
    }else if(!myreg.test(phone)){
        message = "请输入有效的手机号码";
    }
    return message;
}
// 验证密码,规则: 大小写,数字,符号,必须有三种
function vailPassword(password){
    var message = "";
    // var myreg = /^(?![a-zA-Z]+$)(?![A-Z0-9]+$)(?![A-Z\W_]+$)(?![a-z0-9]+$)(?![a-z\W_]+$)(?![0-9\W_]+$)[a-zA-Z0-9\W_]{6,15}$/;
    var myreg = /^(?![A-Z]+$)(?![a-z]+$)(?!\d+$)(?![\W_]+$)\S{6,15}$/;
    if(password == ''){
        message = "密码不能为空";
    }else if(!myreg.test(password)){
        // message = "密码需为6-15位并且包含数字,大小写字母,特殊符号,其中的三种";
        message = "密码需为6-15位并且包含数字,大小写字母,特殊符号,其中的两种";
    }
    return message;
}
