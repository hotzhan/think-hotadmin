

function showAlls()
{
    $(function () { //页面加载完成后执行
        //显示全部
        $('.showAll').on('click', function (){
            // console.log(this)
            if($(this).attr('aria-expanded-all') == 'true')
            {
                showAll('false');
                $(this).attr('aria-expanded-all', 'false')
                $(this).children().eq(1).text('显示全部')
                $(this).children().first().removeClass('fa-minus')
                $(this).children().first().addClass('fa-plus')
            }

            else
            {
                showAll('true');
                $(this).attr('aria-expanded-all', 'true')
                $(this).children().eq(1).text('隐藏全部')
                $(this).children().first().removeClass('fa-plus')
                $(this).children().first().addClass('fa-minus')
            }

        })
    });
}


/**
 * 递归生成父子数组
 * @param data
 * @param pid
 * @returns {*[]}
 */
function changeData(data, pid=0){
    let arr = [];
    $.each(data, function (index, value) {
        //value出现undefined 少了常规管理
        if(data[index] != 0 && value.parent_id == pid){
            arr[value.id] = value;
            //data.splice(index, 1);
            data[index] = 0;
            arr[value.id]['children'] = changeData(data, value.id);
        }
    })
    return arr;
}

/**
 * 规则添加或者编辑页面 ajax获取规则，并用generateSelectTree()函数生成下拉选择列表
 * @param url
 */
function getRules(url) {
    $.ajax({
            url: url,//{:_url('rules')}
            dataType: 'json',
            type: 'get',
            data: {'type':0},
            complete: function () {
                console.log('%cajax提交完成x', ';color:#00a65a');
            },
            success: function (result) {
                console.log('%cajax提交完成x', ';color:#00a65a');
                console.log(result.data);
                console.log($('#parent_id').children().first());
                var data = changeData(result.data);
                $('#parent_id').children().first().after(generateSelectTree(data));
            },
            error: function (xhr, type, errorThrown) {
                console.log('error');
            }
        }
    );
}

/**
 * 生成规则添加或者编辑页面的父级规则菜单选择列表
 * @param data
 * @param pidKey
 * @param paddingLeft
 * @returns {string}
 */
function generateSelectTree(data, pidKey='parent_id', paddingLeft='') {
    var html = '';

    var pdStr = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    paddingLeft += pdStr;//子菜单缩进
    // 遍历每个节点
    for (var i in data) {
        var selt = '';
        if(data[i].id == getUrlParam(pidKey))
            selt = 'selected';

        if(data[i].parent_id === 0)
            paddingLeft = '';//顶级菜单

        html += '<option value="' + data[i].id + '" ' + selt + '>' + paddingLeft + data[i].name + '</option>';
        // 如果该节点还有子节点，则递归生成子菜单
        if (!$.isEmptyObject(data[i].children)) {
            html += generateSelectTree(data[i].children, pidKey, paddingLeft) ;
        }
    }
    return html;
}

/**
 * 递归折叠列表
 * @param data
 * @param paddingLeft
 * @returns {string}
 */
function generateTableTree(data, paddingLeft=0) {
    var html = '';
    var lt = 0.75;
    paddingLeft += lt;//子菜单缩进
    // 遍历每个节点
    for (var i in data) {
        var className = '';
        var ariaExpd = '';

        if (!$.isEmptyObject(data[i].children))
        {
            ariaExpd = 'aria-expanded="false"';
            className += 'expandable';
        }
        if(paddingLeft > lt)//如果paddingLeft不为8说明是子节点
            className += ' submenu';

        html += '<tr class="'+className+'" data-id="' + data[i].id + '" data-parent-id="' + data[i].parent_id + '" '+ariaExpd+'>';
        html += '<td>' + data[i].id + '</td>';
        html += ' <td class="text-left" style="padding-left:' + paddingLeft + 'rem;" onclick="itemToggle($(this).closest(\'tr\'))">';
        if (!$.isEmptyObject(data[i].children))
            html += '<i class="fas fa-caret-right"></i> ';
        html += data[i].name;
        html += '</td>';
        html += '<td class="text-left">' + data[i].rule + '</td>';
        html += '<td>' + data[i].parent_id + '</td>';
        html += '<td><i class="' + data[i].icon + '"></i></td>';
        html += '<td>' + data[i].sort_number + '</td>';
        html += '<td>' + data[i].is_menu_text + '</td>';
        html += '<td>' + data[i].is_show_text + '</td>';
        html += '<td>' + data[i].log_method + '</td>';
        html += '<td class="option">';
        html += '<button class="btn btn-xs btn-primary mr-1 add" data-pid="' + data[i].id + '"><i class="fa fa-plus"></i> 添加</button>';
        html += '<button class="btn btn-xs btn-secondary mr-1 AjaxButton multiAdd"  data-url="" data-id="' + data[i].id + '" data-confirm-content=""><i class="far fa-plus-square"></i> 批加</button>';
        html += '<button class="btn btn-xs btn-info mr-1 edit" data-id=' + data[i].id + ' data-pid=' + data[i].parent_id + '><i class="fa fa-edit"></i> 修改</button>';
        html += '<button class="btn btn-xs btn-danger AjaxButton del" data-url="" data-id="' + data[i].id + '"><i class="fa fa-trash"></i> 删除</button>';
        html += '</td>';
        html += '</tr>';

        // 如果该节点还有子节点，则递归生成子菜单
        if (!$.isEmptyObject(data[i].children)) {
            html += generateTableTree(data[i].children, paddingLeft) ;
        }

    }
    return html;
}

/**
 * 显示全部
 * @param aria
 */
function showAll(aria)
{
    $.each($(".expandable"), function (index, item){
        itemToggle(item, true, aria)
    })
}

/**
 * 显示隐藏子节点
 * @param obj
 */
function itemToggle(obj, all=false, aria='false'){
    var id = $(obj).data('id')
    var element = $('[data-parent-id="'+id+'"]')
    var ariaExpanded = 'aria-expanded'
    if(all == true)
    {
        $(obj).attr(ariaExpanded, aria)
        // console.log($(obj).attr(ariaExpanded))
    }
    if($(obj).attr(ariaExpanded) == 'true')
    {
        // console.log('show')
        //显示子节点
        $.each(element, function (index, item){
            $(item).show()
        })
        $(obj).attr(ariaExpanded, 'false');
        //设置箭头图标
        $(obj).children().eq(1).children().first().removeClass('fa-caret-right');
        $(obj).children().eq(1).children().first().addClass('fa-caret-down');

    }
    else if($(obj).attr(ariaExpanded) == 'false')
    {
        // console.log('hide')
        //隐藏子节点
        $.each(element, function (index, item){
            $(item).hide();
            //如果该节点有子节点，递归遍历隐藏
            if(element.length > 0 && $(item).hasClass('expandable'))
            {
                itemToggle(item, true, 'false');
            }
        })
        $(obj).attr(ariaExpanded, 'true');
        //设置箭头图标
        $(obj).children().eq(1).children().first().removeClass('fa-caret-down');
        $(obj).children().eq(1).children().first().addClass('fa-caret-right');
    }
}

/**
 * add
 * @param url
 */
function initAddAction(url)
{
    //必须等table列表加载完成后才可以
    $('.add').on('click', function () {
        var pid = $(this).data('pid');
        window.location = url + '?parent_id=' + pid;
    });
}

function initEditAction(url)
{
    //必须等table列表加载完成后才可以
    $('.edit').on('click', function () {
        var id = $(this).data('id');
        var pid = $(this).data('pid');
        window.location = url+ '?id=' + id + '&parent_id=' + pid;
    });
}

function initMultiAddAction(url)
{
    //必须等table列表加载完成后才可以
    $.each($('.multiAdd'), function (index, item) {
        $(item).data('url', url);
        $(item).data('confirm-content', '在本规则下增加[查看/添加/编辑/删除]规则节点');
    });
}

function initDelAction(url)
{
    //必须等table列表加载完成后才可以
    $.each($('.del'), function (index, item) {
        $(item).data('url', url);
    });
}

//--------------------授权选择---------------------------end

//--------------------左侧菜单---------------------------start
function getMenus(url) {
    //获取菜单数据
    $.ajax({
            url: url,
            dataType: 'json',
            type: 'get',
            data: {'type':1},
            complete: function () {
                //console.log('%cajax提交完成x', ';color:#00a65a');
            },
            success: function (result) {
                //console.log('%cajax提交完成x', ';color:#00a65a');
                //console.log(result.data);
                //生成菜单树
                $('.nav-sidebar').html(generateMenuTree(result.data));
                //菜单完成加载后一些初始化
                setCurrentOpenMenu();
                initMenuClick();
            },
            error: function (result) {
                console.log('error');
                //console.log(result);
            }
        }
    );
}

/**
 * 菜单点击高亮显示/展开/隐藏
 */
function initMenuClick() {

    //点击子菜单
    $('.nav-sidebar .nav-item ul li:not(.nav-link) > a').on('click', function () {
        // console.log('点击子节点')
        //let active = $('.nav-sidebar').find('.active');
        //active.removeClass('.active');
        //console.log(active.text());
        $(this).addClass('active');
        let $parents = $(this).parents('li');
        // console.log($(this));
        // console.log($parents);
        $parents.find('a:first').addClass('active');//父节点高亮
        $parents.siblings().find('a').removeClass('active');

    });
    //点击父菜单
    $('.nav-sidebar > li > a').on('click', function () {
        // console.log('点击父节点')
        // console.log($(this))
        // console.log($(this).attr('href'))
        //$(this).addClass('active');
        var ahref = $(this).attr('href')

        if(ahref != '#')
        {
            //点击独立菜单
            $(this).addClass('active');
        }

        var $parent = $(this).closest('li');
        console.log($parent)

        if(ahref != '#' || (ahref === '#' && !$parent.hasClass('menu-open')) )
        {
            // console.log('menu-open')
            //点击的是未展开的其它父菜单
            let $parents = $(this).parents('ul');
            //console.log($parents)
            let $menuopen = $parents.find('.menu-open');
            // console.log($menuopen);
            $menuopen.find('ul').removeAttr('style');
            $menuopen.find('.active').removeClass('active');
            $menuopen.removeClass('menu-open');
            $menuopen.removeClass('menu-is-opening');
        }
    });

    $('[data-toggle="popover"]').popover();
}

/**
 * 递归生成菜单树，可无限层级
 * @param data
 * @returns {string}
 */
function generateMenuTree(data, paddingLeft=0) {
    var html = '';
    var noPjax = '';
    paddingLeft += 8;//子菜单缩进
    // 遍历每个节点
    for(var i in data) {
        html += '<li class="nav-item">';
        if(!$.isEmptyObject(data[i].children))
            html += '<a href="#" class="nav-link" style="padding-left:'+paddingLeft+'px;">';
        else
        {
            //if(data[i].rule.indexOf('auth_rule') !== -1)
            //    noPjax = 'no-pjax';//规则菜单界面里有ajax js运行，不走pjax
            html += '<a href="'+data[i].url+'" class="nav-link '+ noPjax +'" style="padding-left:'+paddingLeft+'px;"  >';
        }

        html += '<i class="nav-icon '+ data[i].icon +'"></i>';
        html += '<p>';
        html += data[i].name;
        if(!$.isEmptyObject(data[i].children))
            html += '<i class="right fas fa-angle-left"></i>';
        html += '</p>';
        html += '</a>';
        // 如果该节点还有子节点，则递归生成子菜单
        if(!$.isEmptyObject(data[i].children)) {
            html += '<ul class="nav nav-treeview">' + generateMenuTree(data[i].children, paddingLeft) + '</ul>';
        }
        html += '</li>';
    }
    return html;
}

/**
 * 展开当前链接对应的菜单
 */
function setCurrentOpenMenu(){
    // jQuery代码
    //var currentUrl = window.location.href; // 获取完整的URL地址
    var pathName = window.location.pathname; // 获取去除域名后的路径部分
    var element =getElementByPath(pathName);
    //console.log(element)
    element.addClass('active')
    openParentMenuBar(element)
}

/**
 * 递归给父菜单Bar添加展开样式
 * @param element
 */
function openParentMenuBar(element)
{
    var ul = element.parent().parent();
    if(ul.hasClass('nav-treeview') && ul.parent().hasClass('nav-item'))
    {
        ul.parent().addClass('menu-is-opening menu-open');
        openParentMenuBar(ul)
    }
}
function getElementByPath(path)
{
    var href = "[href='"+path+"']";
    //console.log(href)
    var element = $('.nav-sidebar').find(href); // 根据href属性值为"https://www.example2.com"进行查询
    if(element.length == 0)
    {
        var index = path.lastIndexOf('/');
        if(index>0)
        {
            var subpath = path.slice(0, index);
            element = getElementByPath(subpath);
        }
        //console.log(subpath);
    }
    return element;
}
//--------------------左侧菜单---------------------------end

/**
 * 规则节点html加载完成后一些处理操作
 */
function afterRulesLoad(result)
{
    $('#checkAll').html(checkboxHtml(0, '全选', -1));

    //设置不要折叠，必须等到html内容加载完成才可以，不然获取不到.noFold
    $(".noFold").on('click', function (){
        $(this).closest('tr').attr('aria-expanded', false);
    });

    $("#checkAll").click(function(){
        var chkAll = $('#checkboxPrimary0');
        var chks = $("input[type='checkbox'][name='ruleIds[]']");
        if(chkAll.prop('checked')){
            // console.log('checked')
            chks.prop("checked",true);
        }else{
            // console.log('unchecked')
            //chkAll.attr('checked','checked');
            chks.prop("checked",false);
        }
    });

    //根据全选checkbox状态设置name
    $("#checkboxPrimary0").on('change',function () {
        if($(this).is(":checked")){ //如果复选框被选中
            // console.log('被选中');
            $('#checkboxPrimary0').attr('name', 'allIds');//全部选中，name
        } else {
            // console.log('未被选中');
            $('#checkboxPrimary0').attr('name', 'ruleIds[]');
        }
    });

    //根据各节点状态遍历，重新设置节点checkbox状态
    generateAccessTreeChk(result.data);//遍历规则树节点
    setChkByChilds(0);//最顶级节点，全选节点
}

/**
 * 递归遍历节点
 * @param data
 * @param paddingLeft
 * @param cchildren
 * @returns {string}
 */
function generateAccessTree(data, ids, paddingLeft=0, cchildren=true) {
    var html = '';
    var lt = 0.75;
    var className = '';

    if(paddingLeft === 0)
        className = 'bg-gray1';
    else if(paddingLeft === 0.75)
        className = 'bg-gray2';

    html += '<table class="table table-hover table-left table-bordered">';
    html += '<tbody>';

    paddingLeft += lt;//子菜单缩进

    if(cchildren)
    {
        //data各节点还有子节点
        for (var i in data) {
            // 如果该节点还有子节点，则递归生成子菜单
            if ($.isEmptyObject(data[i].children)) {
                html += '<tr class="'+className+'">';
                html += '<td>';
                html += '<i class="" style="padding-left:1.25rem;"></i>';
                html += checkboxHtml(data[i].id, data[i].name, data[i].parent_id, ids);
                html += '</td>';
                html += '</tr>';
            }
            else
            {
                //判断是否有孙节点
                var flag = false;
                for (var j in data[i].children)
                {
                    if(!$.isEmptyObject(data[i].children[j].children))
                    {
                        flag = true;
                        break;
                    }
                }
                html += '<tr class="'+className+'" data-widget="expandable-table" aria-expanded="true">';
                html += '<td>';
                html += '<i class="expandable-table-caret fas fa-caret-right fa-fw"></i>';
                html += checkboxHtml(data[i].id, data[i].name, data[i].parent_id, ids);
                html += '</td>';
                html += '</tr>';
                html += '<tr class="expandable-body">';
                html += '<td>';
                html += '<div class="p-0">';
                html += generateAccessTree(data[i].children, ids, paddingLeft, flag);
                html += '</div>';
                html += '</td>';
                html += '</tr>';
            }
        }
    }
    else
    {
        //data是最后的节点了，没有子节点了
        html += '<tr>';
        html += '<td>';
        for (var i in data) {
            html += checkboxHtml(data[i].id, data[i].name, data[i].parent_id, ids, 'ml-4');
        }
        html += '</td>';
        html += '</tr>';
    }

    html += '</table>';
    html += '</tbody>';
    return html;
}

/**
 * 递归遍历规则树节点checkbox状态
 * @param data
 */
function generateAccessTreeChk(data)
{
    for (var i in data) {

        if (!$.isEmptyObject(data[i].children)){
            generateAccessTreeChk(data[i].children);
        }
        setChkByChilds(data[i].id);
    }
}

/**
 * checkbox的html代码块
 * @param id
 * @param name
 * @param className
 * @returns {string}
 */
function checkboxHtml(id, name, parent_id, ids=[], className=''){
    var html = '';
    var chkd = '';
    if(ids.includes(id))
        chkd = 'checked';
    //html += '<div class="icheck-primary d-inline ' + className + '">';//伪元素样式，不过无法显示checkbox第3种状态
    html += '<div class="d-inline ' + className + '">';
    html += '<input class="noFold mr-1" type="checkbox" value="'+ id +'" data-parent-id="'+parent_id+'" name="ruleIds[]" id="checkboxPrimary' + id + '" '+ chkd +' onclick="setCheckBox(this)">';
    html += '<label class="noFold" for="checkboxPrimary' + id + '">';
    html += name;
    html += '</label>';
    html += '</div>';
    return html;
}

/**
 * 递归遍历设置子节点状态
 * @param obj
 */
function setChildChk(obj)
{
    var id = $(obj).val();
    var element = $('[data-parent-id="'+id+'"]');
    $.each(element, function (index, item) {

        if($(item).prop("indeterminate"))
            $(item).prop("indeterminate", false).change();//需加.change() 否则checkbox无法监听change状态改变

        if($(obj).prop("checked"))
            $(item).prop("checked", true).change();
        else
            $(item).prop("checked", false).change();

        setChildChk(item);//递归遍历子节点

    });
}

function setCheckBox(obj)
{
    //$(obj).closest('tr').attr('aria-expanded', false);//设置不要折叠

    setParentChk(obj);
}
/**
 * 设置父节点checkbox状态
 * @param obj
 */
function setParentChk(obj, child=true)
{
    var id = $(obj).val();
    var elementParentId = $("#checkboxPrimary"+id).data('parent-id');
    setChkByChilds(elementParentId);

    if(id!=0)
        setParentChk($("#checkboxPrimary"+elementParentId), false);

    if(child)
        setChildChk(obj);
}

/**
 * 根据子节点状态设置当前节点状态
 * @param id
 */
function setChkByChilds(id)
{
    var element = $('[data-parent-id="'+id+'"]');
    var chkd = false;
    var uchkd = false;
    var indt = false;
    $.each(element, function (index, item) {
        //console.log(item);
        if($(item).prop("checked") == true)
            chkd = true;
        else
            uchkd = true;

        if($(item).prop("indeterminate") == true)
            indt = true;
    });

    if(chkd && uchkd==false && indt == false)
    {
        $("#checkboxPrimary"+id).prop("indeterminate", false).change();
        $("#checkboxPrimary"+id).prop("checked", true).change();
    }

    if(chkd && uchkd && indt == false)
    {
        $("#checkboxPrimary"+id).prop("indeterminate", true).change();
        $("#checkboxPrimary"+id).prop("checked", true).change();//父id也要写入数据库
    }

    if(indt == true)
    {
        $("#checkboxPrimary"+id).prop("indeterminate", true).change();
        $("#checkboxPrimary"+id).prop("checked", true).change();//父id也要写入数据库
    }

    if(chkd==false && uchkd)
    {
        $("#checkboxPrimary"+id).prop("indeterminate", false).change();
        $("#checkboxPrimary"+id).prop("checked", false).change();
    }
}
