
//需要引入toastr
toastr.options = {
    "closeButton": true,// 是否显示关闭按钮
    //"progressBar":true,// 是否显示进度条
    "positionClass": "toast-top-right",// 弹出窗的位置
    "showDuration": "1000",// 显示的动画时间
    "hideDuration": "1000",// 消失的动画时间
    "timeOut": "3000",// 弹窗展现时间
    "showEasing": "swing",//显示时的动画缓冲方式
    "hideEasing": "linear",//消失时的动画缓冲方式
    "showMethod": "fadeIn",//显示时的动画方式
    "hideMethod": "fadeOut", //消失时的动画方式
    "allowHtml":true,// 允许弹窗内容包含 HTML 语言
};

//--------------------------------JQuery.validate--------------------------------start
//JQuery.validate设置默认相关处理
$.validator.setDefaults({
    errorElement: 'span',
    //errorClass: "help-block error",

    errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');
        //element.closest('.form-group').append(error);
        element.closest('.formInput').append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
    },

    submitHandler: function (form) {
        //form.submit()//直接提交方式
        submitForm(form)
    }
})



//自定义cRequired方法 参数1：方法名，参数2：规则(可以是自定义函数)，参数3：提示信息message
$.validator.addMethod("cRequired", $.validator.methods.required,"");//也可以自定义其它的规则，比如手机号邮箱等
//自定义required class作为验证，就是元素里有class="required"的全部适用该规则
$.validator.addClassRules("required", { cRequired: true });//使用上面刚定义的cRequired


//--------------------------------JQuery.validate--------------------------------end




/**
 * 表单提交 ajax
 * 参考文章：https://blog.csdn.net/a460550542/article/details/120127150
 * @param form 表单dom
 * @param successCallback 成功回调
 * @param failCallback 失败回调
 * @param errorCallback 错误回调
 * @param showMsg 是否显示弹出信息
 * @returns {boolean}
 */
function submitForm(form, successCallback, failCallback, errorCallback, showMsg = true)
{
    let loadT = layer.msg('正在提交，请稍候…', {icon: 16, time: 0, shade: [0.3, "#000"], scrollbar: false,});
    let action = $(form).attr('action');
    let method = $(form).attr('method');
    let data = new FormData($(form)[0]);

    if (adminDebug) {
        console.log('%c开始提交表单xx!', ';color:#333333');
        console.log('action:', action);
        console.log('method:', method);
        console.log('data:', data);
    }

    $.ajax({
        url: action,
        dataType: 'json',
        type: method,
        data: data,
        contentType: false,
        processData: false,
        complete: function () {
            console.log('%cajax提交完成x', ';color:#00a65a')
        },
        success: function (result) {
            layer.close(loadT);
            layer.msg(result.msg, {
                icon: result.code === 200 ? 1 : 2,
                scrollbar: false,
            });

            // 调试信息
            if (adminDebug) {
                console.log('ajax请求成功x!');
                console.log(result.ispjax);
                console.log(result);
                result.code === 200 ? console.log('%c业务返回成功x', ';color:#00a65a') : console.log('%c业务返回失败', ';color:#f39c12');
            }

            if (result.code === 200) {
                console.log(successCallback);
                if (successCallback) {
                    // 如果有成功回调函数就走回调函数
                    successCallback(result);
                } else {
                    // 没有回调函数跳转url
                    goUrl(result.url, result.ispjax);
                }
            } else {
                if (failCallback) {
                    // 如果有失败回调函数就走回调函数
                    failCallback(result);
                    toastr.error(result.msg);
                } else {
                    // 没有回调函数跳转url
                    goUrl(result.url, result.ispjax);

                }
            }
        },
        error: function (xhr, type, errorThrown) {
            layer.close(loadT);
            // 调试信息
            if (adminDebug) {
                console.log('%c submit fail!', ';color:#dd4b39');
            }

            if (showMsg) {
                showAjaxError(xhr, type, errorThrown);
            }

            if (errorCallback) {
                errorCallback(xhr)
            }

        }
    });

}

/**
 * ajax请求封装
 * @param url 访问的url
 * @param method  访问方式
 * @param data  data数据
 * @param go 要跳转的url
 */
function ajaxRequest(url, method, data, go) {
    let loadT = layer.msg('正在请求,请稍候…', {icon: 16, time: 0, shade: [0.3, '#000'], scrollbar: false,});
    $.ajax({
            url: url,
            dataType: 'json',
            type: method,
            data: data,
            complete: function () {
                console.log('%cajax提交完成x', ';color:#00a65a')
            },
            success: function (result) {
                layer.close(loadT);
                layer.msg(result.msg, {
                    icon: result.code === 200 ? 1 : 2,
                    scrollbar: false,
                });

                if (adminDebug) {
                    console.log('request successxx!');
                    console.log(result);
                }
                if (result.code === 200) {
                    console.log('%c result success', ';color:#00a65a');
                } else {

                }

                goUrl(result.url, result.ispjax);
            },
            error: function (xhr, type, errorThrown) {

                layer.close(loadT);
                if (adminDebug) {
                    console.log('%c request fail!', ';color:#dd4b39');
                    console.log("url:" + url);
                    console.log("data:", data);
                }

                showAjaxError(xhr, type, errorThrown);

            }
        }
    );
}
/**
 * ajax访问按钮
 * 例如元素为<a class="AjaxButton" data-confirm="1" data-type="1" data-url="disable" data-id="2" data-go="" ></a>
 * data-confirm为是否弹出提示，1为是，2为否。比如删除某条数据，data-confirm="1"就会弹出来提示
 * data-type为访问方式，1为直接ajax访问，例如删除操作。2是为打开layer窗口展示数据，例如查看操作日志详情
 * data-url为要访问的url
 * data-id为要操作的数据ID，可以填写正常的数据ID，例如data-id="2"，
 * 或者填写checked表示获取当前数据列表选择的ID，也就是取的变量dataSelectIds的值
 * data-go为操作完成后的跳转url，不设置此参数默认根据后台返回的url跳转
 * data-confirm-title为确认提示弹窗的标题 例如data-confirm-title="删除警告"
 * data-confirm-content为确认提示的内容 例如data-confirm-content="您确定要删除此数据吗？"
 * data-title 窗口显示的标题
 *
 */
$(function () {
    $('body').on('click', '.AjaxButton', function (event) {
        event.preventDefault();
        if (adminDebug) {
            console.log('点击Ajax请求按钮');
        }

        let dataData = {};

        // 是否弹出提示
        let layerConfirm = parseInt($(this).data("confirm") || 1);
        //访问方式，1为直接访问，2为layer窗口显示
        let layerType = parseInt($(this).data("type") || 1);
        //访问的url
        let url = $(this).data("url");
        //访问方式，默认post
        let layerMethod = $(this).data("method") || 'post';
        //访问成功后跳转的页面，不设置此参数默认根据后台返回的url跳转
        let go = $(this).data("go") || 'url://reload';

        //当为窗口显示时可定义宽度和高度
        let layerWith = $(this).data("width") || '80%';
        let layerHeight = $(this).data("height") || '60%';

        //窗口的标题
        let layerTitle = $(this).data('title');

        //当前操作数据的ID
        let dataId = $(this).data("id");

        //如果没有定义ID去查询data-data属性
        if (dataId === undefined) {
            dataData = $(this).data("data") || {};
        } else {
            if (dataId === 'checked') {
                if (dataSelectIds.length === 0) {
                    layer.msg('请选择要操作的数据', {icon: 2, scrollbar: false,});
                    return false;
                }
                dataId = dataSelectIds;
            }
            dataData = {"id": dataId};
        }
        console.log(dataData)
        if (typeof (dataData) != 'object') {
            //dataData = JSON.parse(dataData);
            dataData = eval('(' + dataData + ')');//对字符串要求不高，书写自由度高
        }
        console.log(dataData)
        /*需要确认操作*/
        if (layerConfirm === 1)
        {
            //提示窗口的标题
            let confirmTitle = $(this).data("confirmTitle") || '操作确认';
            //提示窗口的内容
            let confirmContent = $(this).data("confirmContent") || '您确定要执行此操作吗?';
            console.log($(this).data("confirmContent"));
            console.log(confirmContent);
            layer.confirm(confirmContent, {title: confirmTitle, closeBtn: 1, icon: 3}, function () {
                //如果为直接访问
                if (layerType === 1) {
                    ajaxRequest(url, layerMethod, dataData);
                } else if (layerType === 2) {
                    //如果为打开窗口
                    //先进行权限查询
                    /*if (checkAuth(url)) {
                        layer.open({
                            type: 1,
                            area: [layerWith, layerHeight],
                            title: layerTitle,
                            closeBtn: 1,
                            shift: 0,
                            content: url + "?request_type=layer_open&" + parseParam(dataData),
                            scrollbar: false,
                        });
                    }*/
                }
            });
        } else if (layerType === 1) {
            //直接请求
            ajaxRequest(url, layerMethod, dataData);
        } else if (layerType === 2) {
            //弹出窗口
            //检查权限
            /*if (checkAuth(url)) {
                //用窗口打开
                layer.open({
                    type: 2,
                    area: [layerWith, layerHeight],
                    title: layerTitle,
                    closeBtn: 1,
                    shift: 0,
                    content: url + "?request_type=layer_open&" + parseParam(dataData),
                    scrollbar: false,
                });
            }*/
        }
    });
});



/** 跳转到指定url */
function goUrl(url, ispjax= true) {
    console.log('链接' + url)
    if (url === '' || url === undefined) {
        return;
    }

    //清除列表页选择的ID
    if (url !== 'url://current' && url !== 1) {
        dataSelectIds = [];
    }
    if (url === 'url://current' || url === 1) {
        console.log('Stay current page.');
    } else if (url === 'url://reload' || url === 2) {
        console.log('Reload current page.');
        if(ispjax)
            $.pjax.reload();
        else
            window.location.reload();
    } else if (url === 'url://back' || url === 3) {
        //返回后 pjax会自动更新页面内容
        console.log('Return to the last page.');
        window.history.go(-1);
    }  else if (url === 4 || url === 'url://close_refresh') {
        console.log('Close this layer page and refresh parent page.');
        let indexWindow = parent.layer.getFrameIndex(window.name);
        //先刷新父级页面
        parent.goUrl(2);
        //再关闭当前layer弹窗
        parent.layer.close(indexWindow);
    } else if (url === 5 || url === 'url://close_layer') {
        console.log('Close this layer page.');
        let indexWindow = parent.layer.getFrameIndex(window.name);
        parent.layer.close(indexWindow);
    } else {
        console.log('Go to ' + url);
        if(ispjax)
        {
            try
            {
                $.pjax({
                    url: url,
                    container: '#pjaxContainer'
                });
            }
            catch (e)
            {
                window.location.href = url;
            }
        }
        else
        {
            //直接访问不走pjax
            window.location.href = url;
        }


    }
}

function showAjaxError(xhr, type, errorThrown) {
    let errorTitle;
    // 调试信息
    if (adminDebug) {
        console.log('xhr', xhr);
        console.log('xhr.responseJSON', xhr.responseJSON);
    }

    if(xhr.responseJSON !== undefined && xhr.responseJSON.code !== 'undefined')
    {
        if ( xhr.responseJSON.code === 500) {
            errorTitle = xhr.responseJSON.msg;
        } else {
            errorTitle = '系统繁忙,状态码：' + xhr.status + ',参考信息：' + (xhr.responseJSON.message || xhr.responseJSON.msg || '');
        }
    }
    else
    {
        errorTitle = '服务端异常';
    }
    layer.msg(errorTitle, {icon: 2, scrollbar: false,});
}


