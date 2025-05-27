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

    deleteAllCookiesByKey(cookiePrefix + pagesizeStr);
    //Cookies.set(cookiePrefix + 'pagesize', listnum, {expires: 365});
    $.cookie(cookiePrefix + pagesizeStr, listnum, {expires: 365, path: '/'});
    if(totalnum > listnum)
    {
        // $.pjax.reload(); // 不能用这个，会reload到原始的pjax页
        pjaxReloadCurrent();
    }
    else
    {
        //每页显示数量比总数多，页数溢出，跳转回第一页
        let url = window.location.href;
        let newUrl;
        if (/([?&])page=\d+/.test(url)) {
            newUrl = url.replace(/([?&])page=\d+/, '$1page=1');
        } else if (url.indexOf('?') > -1) {
            newUrl = url + '&page=1';
        } else {
            newUrl = url + '?page=1';
        }
        pjaxReloadCurrent(newUrl);
    }
}

function deleteAllCookiesByKey(key) {
    const paths = location.pathname
        .split('/')
        .filter(Boolean)
        .map((_, i, arr) => '/' + arr.slice(0, i + 1).join('/'));
    paths.unshift('/'); // 添加根路径

    paths.forEach(path => {
        $.removeCookie(key, { path: path });
    });
}