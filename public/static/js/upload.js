/**
 * 初始化上传单图
 * 使用 bootstrap-fileinput
 * 参考资料 http://www.bootstrap-fileinput.com/
 * @param field
 * @param fileId
 */
function initUploadImg(field, fileId = '') {
    initUploadFile(field, fileId, 'image');
}

/**
 * 初始化上传单视频
 * @param field
 * @param fileId
 */
function initUploadVideo(field, fileId = '') {
    initUploadFile(field, fileId, 'video');
}

/**
 * 初始化上传多图
 * @param field
 * @param fileId
 */

function initUploadMultiImg(field, fileId = '') {
    initUploadMultiFile(field, fileId, 'image')
}


/**
 * 初始化上传多文件
 * @param field
 * @param fileId
 * @param fileType
 */

function initUploadMultiFile(field, fileId = '', fileType = '') {
    let $field = $('#' + field);
    fileId = fileId || field + '_file';
    console.log($field);
    console.log(fileId);
    let $fileDom = $("#" + fileId);
    let initialPreview;
    let initialPreviewConfig = [];

    let typeCn = '文件';
    switch (fileType) {
        default:
            break;
        case "image":
            typeCn = '图片';
            break;
        case "video":
            typeCn = '视频';
            break;
    }

    if ($field.val().length > 0) {
        initialPreview = $field.val().split(',');
        console.log(initialPreview);
        $.each(initialPreview, function (index, value) {
            console.log(index);
            var fileName = value.split('/').pop();
            initialPreviewConfig[index] = {
                downloadUrl: value,
                //url: fileDelUrl + '?file=' + value,//删除文件url，如果该项没有，默认使用deleteUrl
                extra: {file:value},//对象或者函数，通过POST方法传递给初始预览的删除url或者AJAX服务器响应的额外数据
                caption: fileName,//文件名
                //size: 2003923,文件大小
                type:fileType,//如果不设置默认是image
            };
        });
    }


    $fileDom.fileinput({
        theme: 'fas',
        language: 'zh',
        showDrag: true,
        showClose: false,
        showBrowse: true,
        showUpload: false,
        showRemove: false,
        uploadAsync: true,
        showCaption: false,
        showPreview: true,
        showCancel: false,
        showDownload: false,
        overwriteInitial: false,
        browseOnZoneClick: true,
        initialPreviewAsData: true,
        uploadUrl: uploadUrl,//上传文件url，在base.html里定义
        deleteUrl: fileDelUrl,//删除url，在base.html里定义
        minFileCount: 1,
        maxFileCount: 10,
        initialPreviewShowDelete: true,//是否为每个使用initialPreview创建的缩略图显示删除按钮
        dropZoneTitle: '点此上传' + typeCn + '或将' + typeCn + '拖到这里(支持多' + typeCn + '上传)<br/>',
        dropZoneClickTitle: '(或点击下方选择按钮)',
        fileActionSettings: {
            showDownload: false,
            showDrag: true,
        },
        initialPreviewFileType: fileType,
        uploadExtraData: {
            file_field: fileId,
        },
        initialPreview: initialPreview,
        initialPreviewConfig: initialPreviewConfig,
    }).on("filebatchselected", function (event, files) {
        if (adminDebug) {
            console.log('选择文件');
            console.log(event, files);
        }
        $fileDom.fileinput("upload");
    }).on('fileuploaded', function (event, data, previewId, index) {
        setNewContent($fileDom, $field);
        if (adminDebug) {
            console.log('上传文件');
            console.log(event, data, previewId, index);
        }
    }).on('filedeleted', function (event, key, data) {
        setNewContent($fileDom, $field);
        if (adminDebug) {
            console.log('删除文件');
            console.log(event, key, data);
        }
    }).on('filesorted', function (event, params) {
        setNewContent($fileDom, $field);
        if (adminDebug) {
            console.log('排序文件');
            console.log(event, params);
        }
    });
}


/**
 * 初始化上传单文件
 * @param field
 * @param fileId
 * @param fileType
 */
function initUploadFile(field, fileId = '', fileType = 'image') {
    let $field = $('#' + field);
    var fileName = $field.val().split('/').pop();
    if (adminDebug) {
        console.log('初始化上传文件');
    }
    fileId = fileId || field + '_file';
    let allowedFileTypes = fileType === 'file' ? ['image', 'html', 'text', 'video', 'audio', 'flash', 'object'] : [fileType]
    let initialPreviewFileType =  fileType;
    let $fileDom = $("#" + fileId);
    let typeCn = '文件';
    switch (fileType) {
        case "image":
            typeCn = '图片';
            break;
        case "video":
            typeCn = '视频';
            break;
        default:
            break;
    }
    if (adminDebug) {
        console.log(allowedFileTypes,initialPreviewFileType,$fileDom,typeCn);
    }

    $fileDom.fileinput({
        theme: 'fas',
        language: 'zh',
        showDrag: false,
        showClose: false,
        showBrowse: true,
        showUpload: false,
        showRemove: false,
        uploadAsync: true,
        showCaption: false,
        showPreview: true,
        showCancel: false,
        showDownload: false,
        browseOnZoneClick: true,
        overwriteInitial: true,
        initialPreviewAsData: true,
        uploadUrl: uploadUrl,
        minFileCount: 1,
        maxFileCount: 1,
        autoReplace: true,
        initialPreviewShowDelete: true,//是否为每个使用initialPreview创建的缩略图显示删除按钮
        dropZoneTitle: '点此上传' + typeCn + '或将' + typeCn + '拖到这里…',
        dropZoneClickTitle: '(或点击下方选择按钮)',
        fileActionSettings: {
            showDrag: false,
            showDownload: false,
        },
        allowedFileTypes: allowedFileTypes,
        initialPreviewFileType: initialPreviewFileType,
        uploadExtraData: {
            file_field: fileId,
            file_type: fileType,
        },
        // 初始预览配置
        initialPreviewConfig: [
            {
                url: fileDelUrl + '?file=' + $field.val(),//删除文件url
                caption: fileName,
                //size: 2003923,文件大小
            },
        ]
    }).on("filebatchselected", function (event, files) {
        if (adminDebug) {
            console.log('选择文件');
            console.log(event);
            console.log(files);
        }

        $fileDom.fileinput("upload");
    }).on('fileuploaded', function (event, data, previewId, index) {
        if (adminDebug) {
            console.log('上传文件');
            console.log(event);
            console.log(data);
            console.log(previewId);
            console.log(index);
        }

        let response = data.response;
        if (response.code === 200 && response.initialPreview !== undefined) {
            $('#' + field).val(response.initialPreview[0]);//这里虽然改变了input的value提交也有效，但是在html显示没有改变
            $('#' + field).attr('value', $('#' + field).val());//让html内容也改变，可加可不加
        }
    });
}

/**
 * 设置新的图片
 * @param $fileDom
 * @param $field
 * @returns {*}
 */
function setNewContent($fileDom, $field) {
    let current_preview = ($fileDom.fileinput('getPreview'));
    let preview_content = current_preview.content;
    let new_content = preview_content.join(",");
    $field.val(new_content);

    if (adminDebug) {
        console.log(preview_content);
    }
    return new_content;
}
