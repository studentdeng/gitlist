var mainUrl = '/gitlist/';
function setCookie(name, value, seconds) {
    seconds = seconds || 0;   //seconds有值就直接赋值，没有为0，这个根php不一样。
    var expires = "";
    if (seconds != 0) {      //设置cookie生存时间
        var date = new Date();
        date.setTime(date.getTime() + (seconds * 1000));
        expires = "; expires=" + date.toGMTString();
    }
    document.cookie = name + "=" + escape(value) + expires + "; path=/";   //转码并赋值
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');    //把cookie分割成组
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];                      //取得字符串
        while (c.charAt(0) == ' ') {          //判断一下字符串有没有前导空格
            c = c.substring(1, c.length);      //有的话，从第二位开始取
        }
        if (c.indexOf(nameEQ) == 0) {       //如果含有我们要的name
            return unescape(c.substring(nameEQ.length, c.length));    //解码并截取我们要值
        }
    }
    return false;
}

function make_base_auth(user, password) {
    var tok = user + ':' + password;
    var hash = btoa(tok);
    return "Basic " + hash;
}

//issue page create
function onIssueCreate(issueId, projectId)
{
    var text = $('#issueInputText').val();
    if (text.length == 0)
    {
        alert('input empty');
        return;
    }

    var username = getCookie('username');
    var password = getCookie('password');

    $.ajax({
        url: mainUrl + "worklife/index.php/api/issues/create",
        type: "post",
        beforeSend: function(xhr) {
            xhr.setRequestHeader('Authorization', make_base_auth(username, password));
        },
        data: {
            text: text,
            userid: "1",
            reply_to: issueId,
            project_id: projectId
        },
        success: function(data) {
            location.reload();
        },
        error: function(xhr, data) {
            if (xhr.status == 401)
            {
                window.location.href = mainUrl + "login.html";
            }

            alert('failed');
        }
    });
}

function onUpdateIssueState(issueId, state, projectId)
{
    var username = getCookie('username');
    var password = getCookie('password');
    var nickname = getCookie('nickname');

    var textInput = $('#issueInputText').val();
    var text;

    if (state == 1)
    {
        text = nickname + ' closed this issue and said ';
    }
    else if (state == 0)
    {
        text = nickname + ' reopened this issue and said ';
    }

    if (textInput.length > 0)
    {
        text += textInput;
    }
    else
    {
        text += 'nothing';
    }

    $.ajax({
        url: mainUrl + "worklife/index.php/api/issues/update_state",
        type: "post",
        data: {
            id: issueId,
            state: state
        },
        success: function() {

            $.ajax({
                url: mainUrl + "worklife/index.php/api/issues/create",
                type: "post",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('Authorization', make_base_auth(username, password));
                },
                data: {
                    text: text,
                    userid: "1",
                    reply_to: issueId,
                    project_id: projectId
                },
                success: function(data) {
                    location.reload();
                },
                error: function(xhr, data) {
                    if (xhr.status == 401)
                    {
                        window.location.href = mainUrl + "login.html";
                    }

                    alert('failed');
                }
            });
        },
        error: function(data) {
            console.log(data);
            alert(data.message);
        }
    });
}

//issuelist page create
function onIssueListCreate(projectId)
{
    var text = $('#issueInputText').val();
    if (text.length == 0)
    {
        alert('input empty');
        return;
    }

    var username = getCookie('username');
    var password = getCookie('password');

    $.ajax({
        url: mainUrl + "worklife/index.php/api/issues/create",
        type: "post",
        beforeSend: function(xhr) {
            xhr.setRequestHeader('Authorization', make_base_auth(username, password));
        },
        data: {
            text: text,
            project_id: projectId
        },
        success: function(data) {
            location.reload();
        },
        error: function(xhr, data) {
            if (xhr.status == 401)
            {
                window.location.href = mainUrl + "login.html";
            }

            alert('failed');
        }
    });
}

function userLoginSubmit() {
    var username = $('#usernameLabel').val();
    var password = $('#passwordLabel').val();

    var url = mainUrl + '/worklife/index.php/api/account/login';
    $.ajax({
        url: url,
        type: "post",
        beforeSend: function(xhr) {
            xhr.setRequestHeader('Authorization', make_base_auth(username, password));
        },
        async: false,
        data: {},
        dataType: "json",
        success: function(data) {
            var username = data.username;
            var password = data.password;
            var nickname = data.nickname;

            setCookie("username", username, 3600 * 3);
            setCookie("password", password, 3600 * 3);
            setCookie("nickname", nickname, 3600 * 3);
            
            alert(mainUrl);
            
            window.location.href = mainUrl;
        },
        error: function(data) {
            console.log(data);
            alert(data.message);
        }

    });
}