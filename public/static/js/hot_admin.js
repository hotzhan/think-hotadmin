
/** pjax相关 */
try {
    $.pjax.defaults.timeout = 3000;
    $.pjax.defaults.type = 'GET';
    $.pjax.defaults.container = '#pjaxContainer';
    $.pjax.defaults.fragment = '#pjaxContainer';
    $.pjax.defaults.maxCacheLength = 0;
    $(document).pjax('a:not(a[target="_blank"],.no-pjax)', {	//
        container: '#pjaxContainer',
        fragment: '#pjaxContainer'
    });
    $(document).ajaxStart(function () {
        console.log(this);
        // ajax请求的时候显示顶部进度条
        if (adminDebug) {
            console.log('ajax请求开始');
        }
        //NProgress.start();
    }).ajaxStop(function () {
        // ajax停止的时候结束进度条
        if (adminDebug) {
            console.log('ajax请求停止');
        }
        //NProgress.done();
    });
} catch (e) {
    if (adminDebug) {
        console.log('初始化pjax报错，信息：' + e.message);
    }
}

$(document).on('pjax:timeout', function (event) {
    event.preventDefault();
});
$(document).on('pjax:send', function (xhr) {
    NProgress.start();
});
$(document).on('pjax:complete', function (xhr) {
    //如果pjax容器pjaxContainer里有需要运行的js，可以做成函数放到这里面
    initToolTip();
    initImgViewer();
    setNavTab();
    viewCheckAuth();
    imgLazyLoad();
    NProgress.done();
    selectList();
    showAlls();
});
//列表页搜索pjax
$(document).on('submit', '.searchForm', function (event) {
    $.pjax.submit(event, '#pjaxContainer');
});


/* 初始化 */
$(function () {
    viewCheckAuth();
    setNavTab();
    // 初始化提示
    initToolTip();
    // 初始化图片预览
    initImgViewer();
    //图片懒加载
    imgLazyLoad();

    selectList();
    showAlls();

    let $body = $('body');
    /* 返回按钮 */
    $body.on('click', '.BackButton', function (event) {
        event.preventDefault();
        history.back();
    });

    /* 刷新按钮 */
    $body.on('click', '.ReloadButton', function (event) {
        event.preventDefault();
        $.pjax.reload();
    });
});

/**
 * pjax reload 指定页面或当前页面，避免reload到原始页面
 * $.pjax这个保存的是最开始点击的那个页面
 * @param url
 */
function pjaxReloadCurrent(url='') {
    let reloadUrl = url ? url : window.location.href;
    $.pjax.reload({
        container: '#pjaxContainer',
        url: reloadUrl,
        fragment: '#pjaxContainer'
    });
}

/**
 * 图片预览
 */
function initImgViewer() {
    $('.imgViewer').viewer({
        //url: 'src',
        url: 'data-original',//img懒加载用了data-original
        title: function (obj) {
            return obj.alt;
        }
    });
}


/**
 * 初始化提示
 */
function initToolTip() {
    // 提示泡
    $('[data-toggle="tooltip"]').tooltip({
        container: '#pjaxContainer',
        trigger: 'hover',
    });
}

/** 检查视图内权限 */
function viewCheckAuth() {
    $('.viewCheckAuth').each(function () {
        let $obj = $(this);
        let haveAuth = parseInt($obj.data('auth'));
        if (adminDebug) {
            console.log('检查元素权限', haveAuth);
        }
        if (haveAuth === 1 && !$obj.is(":visible")) {
            $obj.show();
        } else {
            $obj.hide();
        }
    });
}

/**
 * 图片懒加载
 */
function imgLazyLoad()
{
    $("img.lazy").lazyload({
        effect:"show",          //展现的方式,常用：show显示\fadeIn闪现
        //threshold:180,      //在距离屏幕多少px时开始加载
        //event:"scroll",        //懒加载的触发事件,常用：click点击/mouseover鼠标移入/sporty运动/默认为scroll滑动
        //container:$("#main"),   //指定容器内的元素产生效果
        failure_limit:2                //加载多少张可见区域外的图片
    });
}

/**
 * 列表选择，放到函数里可以放到pjax完成后和页面初始化里
 */
function selectList()
{
    $(function () {
        $('#checkAll').on('click', function () {

            if($(this).prop('checked') == true)
            {
                $('.dataListCheck').prop('checked', true).change();
            }
            else
            {
                $('.dataListCheck').prop('checked', false).change();
                dataSelectIds = [];
            }
        });

        $('.dataListCheck').on('change', function () {
            var id = $(this).val();
            console.log('id:' + id);
            var index = $.inArray(id, dataSelectIds);
            if($(this).prop('checked') == true)
            {
                if(index < 0)
                    dataSelectIds.push(id);
            }
            else
            {
                if(index > -1)
                    dataSelectIds.splice(index, 1);
            }

            console.log('当前ID:' + dataSelectIds);
        });
    })
}
