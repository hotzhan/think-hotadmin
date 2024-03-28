<?php

namespace app\admin\controller\setting;

use app\admin\controller\Base;
use app\common\traits\ConfigTrait;
use think\facade\View;
use app\admin\model\ConfigGroup as ConfigGroupModel;
use app\admin\validate\ConfigGroup as ConfigGroupValidate;

Class ConfigGroup extends Base
{
    use ConfigTrait;

    public function index()
    {
        //$list = ConfigGroupModel::where('delete_time', 0)
        //模型中配置了软删除，查询默认自动剔除，无需where('delete_time', 0)
        $list = ConfigGroupModel::order('id', 'desc')
            ->paginate([
            'query'=>$this->request->get(),
            'list_rows'=>$this->admin['pagesize'],
            'var_page'=>'page'
        ]);

        View::assign([
            'list'=>$list,
            'total'=>$list->total(),
            'page'=>$list->render()
        ]);

        return View::fetch();
    }

    public function add()
    {
        if($this->request->isPost())
        {
            $param = $this->request->param();

            /*
             * $codeData = ConfigGroupModel::where('code', $param['code'])->find();
            if($codeData)
            {
                return $this->error('已存在该代码分组配置，请修改代码字符串文本',500);
            }
            */

            $validate = new ConfigGroupValidate();
            $check = $validate->check($param);
            if(!$check)
                return $this->error($validate->getError());

            //避免配置和系统变量或者文件名出现重复冲突，加一下前缀
            $param['code'] = add_prefix($param['code']);

            $res = ConfigGroupModel::create($param);
            if($res)
                return $this->success('添加成功', 200, [], 'setting/config_group/index');
            else
                return $this->error('添加失败',500);
        }
        else
        {
            $moduleList = $this->getAppList();
            View::assign('moduleList', $moduleList);
            return View::fetch();
        }

    }

    public function edit()
    {
        $param = $this->request->param();
        $id = $param['id'];
        $configGroup = ConfigGroupModel::find($id);
        if($this->request->isPost())
        {
            $validate = new ConfigGroupValidate();
            $check = $validate->check($param);
            if(!$check)
                return $this->error($validate->getError());

            //避免配置和系统变量或者文件名出现重复冲突，加一下前缀
            $param['code'] = add_prefix($param['code']);

            $res = $configGroup->save($param);
            if($res)
                return $this->success('修改成功', 200, [], 'index');
            else
                return $this->error('修改失败');
        }
        else
        {
            $moduleList = $this->getAppList();
            View::assign([
                'configGroup' => $configGroup,
                'moduleList' => $moduleList
            ]);
            return View::fetch();
        }
    }

    public function del(ConfigGroupModel $model)
    {
        if($this->request->isPost())
        {
            $param = $this->request->param();
            $id = $param['id'];

            if($model->inNoDeletionIds($id))
                return $this->error('不可删除记录，系统保留用');

            //判断配置分组下面是否有相关配置，如果分组下有配置，则无法直接删除
            $configGroup= ConfigGroupModel::with('config')->find($id);
            if(!empty($configGroup->config->toArray()))
            {
                $str = '';
                foreach ($configGroup->config as $config)
                {
                    $str .= $config['name'].'/';
                }
                $str = mb_substr($str, 0, -1);
                return $this->error('该分组下有【'.$str.'】，无法删除',500);
            }

            //软删除
            $res = ConfigGroupModel::destroy($id);
            if($res)
            {
                //删除配置文件
                $this->deleteConfigFile($configGroup->module);

                return $this->success('删除成功',200, [], URL_RELOAD);
            }
            else
            {
                return $this->error('删除失败',500);
            }
        }
    }

    public function createConfig()
    {
        $param = $this->request->param();
        $id = $param['id'];
        $configGroup = ConfigGroupModel::find($id);
        $this->writeConfigFile($configGroup->module);
        return $this->success('生成完成');
    }

    /**
     * 根据app目录下遍历文件目录获取应用列表
     * @return array
     */
    public function getAppList()
    {
        $appPath = $this->app->getBasePath();
        $dirList = scandir($appPath);
        $diffDirList = ['.', '..', 'facade'];
        $appList = [];
        foreach ($dirList as $value)
        {
            if(!in_array($value, $diffDirList) && is_dir($appPath . $value))
            {
                $appList[] = $value;
            }
        }
        return $appList;
    }

}