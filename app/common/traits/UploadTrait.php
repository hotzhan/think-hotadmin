<?php
/**
 * 文件上传
 * User: hotadmin
 * Date: 2024/1/31
 * Site: https://www.hotadmin.cn
 */


namespace app\common\traits;

use think\facade\Filesystem;
use app\common\model\Attachment as AttachmentModel;
use think\response\Json;

trait UploadTrait
{
    protected function uploadFile($param): Json
    {
        $fileField = $param['file_field'];
        $file = $this->request->file($fileField);

        //文件保存到服务器
        $fileInfo = $this->saveFile($file, $param);

        $data = [
            'code'                 => 200,
            'initialPreview'       => [$fileInfo['url']],
            'initialPreviewAsData' => true,
            'showDownload'         => false,
            //'initialPreviewFileType' => $fileType,
            'initialPreviewConfig' => [
                [
                    'downloadUrl' => $fileInfo['url'],
                    'key'         => $fileInfo['filename'],
                    'caption'     => $fileInfo['filename'],
                    'url'         => _url('del', ['file' => $fileInfo['url']]),
                    'size'        => $fileInfo['filesize'],
                ]
            ],
        ];

        return json($data);
    }

    /**
     * 编辑器图片上传，可用于summernote/ckeditor等编辑器上传
     * @param $param
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function editorUploadFile($field, $param): Json
    {
        $file = $this->request->file($field);//ckeditor上传的文件字段是upload

        //文件保存到服务器
        $fileInfo = $this->saveFile($file, $param);

        if($fileInfo)
        {
            $data = [
                "code" => 200,
                "uploaded" => 1,
                "fileName" => $fileInfo['filename'],
                "url"      => $fileInfo['url'], // url的值参考 /storage/uploads/20251122/eamsd-xdkxxxxxxxxxxxxxxxxxxxxxxxx.jpg
//                "url"      => $this->request->domain() . $fileInfo['url'], // 如果后台和前台不是同一套，则需要加上域名
            ];
        }
        else
        {
            $data = [
                "code" => 400,
                "uploaded" => 0,
                "error"    => "图片上传失败"
            ];
        }


        return json($data);
    }

    protected function delFile($param)
    {
        $file = urldecode($param['file']);
        $path = $this->app->getRootPath() . 'public' . $file;
        $trueDelete = config('filesystem.form_true_delete');
        $result = !$trueDelete || @unlink($path);
        return $result ? json(['message' => '成功',]) : json(['message' => '失败']);
    }

    /**
     * @param $sha1
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function saveFile($file, $param): array
    {
        $fileExist = false;
        $fileInfo = [];
        //文件sha1
        $fileInfo['sha1'] = $file->sha1();

        $fileData = AttachmentModel::where('sha1', $fileInfo['sha1'])->find();
        if($fileData)
        {
            //判断服务器上是否有该文件，这里只是判断本机的，如果是云存储或者分布存储需要修改
            $path = $this->app->getRootPath() . 'public' . $fileData['url'];
            if(file_exists($path))
            {
                $fileExist = true;
            }
        }

        if($fileExist)
        {
            $fileInfo = $fileData;
        }
        else
        {
            //获取filesystem.php文件上传配置
            //$config = config('filesystem.disks.public');
            $config = Filesystem::getDiskConfig('public');

            //保存到服务器后的文件路径文件名
            $saveName = Filesystem::putFile('uploads', $file);
            $saveName = str_replace('\\', '/', $saveName);
            $fileInfo['url'] = $config['url'] . '/' . $saveName;

            //文件原始名
            $fileInfo['filename'] = str_replace("\\", '/', $file->getOriginalName());
            //文件大小
            $fileInfo['filesize'] = $file->getSize();
            //文件mime类型
            $fileInfo['mimetype'] = $file->getMime();
            //文件类型
            $fileInfo['filetype'] = $this->getFileType($fileInfo['mimetype']);

            //根据上传时判断是管理员还是普通用户
            $fileInfo[$param['uidStr']] = $param['uid'];
            //上传记录保存到数据库
            $this->saveToDb($fileInfo);
        }
        return $fileInfo ?? [];
    }

    /**
     * 上传记录保存到数据库
     * @param array $data
     * @return void
     */
    protected function saveToDb(array $data): void
    {
        AttachmentModel::create($data);
    }


    /**
     * 根据文件mime类型判断文件类型
     * @param $mimeType
     * @return string
     */
    protected function getFileType($mimeType): string
    {
        $fileType = 'file';

        if (str_starts_with($mimeType, 'image')) // substr($mimeType, 0, 5) === 'image'
        {
            $fileType = 'image';
        }
        else if (str_starts_with($mimeType,'video')) // substr($mimeType, 0, 5) === 'video'
        {
            $fileType = 'video';
        }
        else
        {

        }

        return $fileType;
    }
}