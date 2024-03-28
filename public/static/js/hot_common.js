/**
 * 点击Tab相关功能
 * @param classStr
 */
function setNavTab(classStr) {
    console.log(classStr);
    console.log($(classStr));
    console.log($(classStr).length);
    if ($(classStr).length === 1) {
        if (adminDebug) {
            console.log('选项卡初始化');
        }
        let hash = document.location.hash;
        if (hash) {
            $(classStr + ' a[href="' + hash + '"]').tab('show');
        } else {
            $(classStr + ' a:first').tab('show');
        }

        $(classStr + ' .nav-item .nav-link').on('click', function () {

            document.location.hash = $(this).attr('href');
        })
    }
}

/**
 * 获取url参数
 * @param name 参数名
 * @returns {string}
 */
function getUrlParam(name) {
    var searchParams = new URLSearchParams(window.location.search);
    return searchParams.get(name);
}
//var paramValue = getUrlParam('paramName');

function setPageSize(obj, pagesizeStr='pagesize')
{
    //var listnum = $(obj).val();
    var listnum = obj.value;
    var totalnum = parseInt($('.listTotal').text());
    //Cookies.set(cookiePrefix + 'pagesize', listnum, {expires: 365});
    $.cookie(cookiePrefix + pagesizeStr, listnum, {expires: 365})
    if(totalnum > listnum)
    {
        $.pjax.reload();
    }
    else
    {
        //每页显示数量比总数多，跳转显示第一页
        var url = window.location.href;
        url = url.replace(/page=\d+/g, 'page=1')
        $.pjax(url);
    }


}