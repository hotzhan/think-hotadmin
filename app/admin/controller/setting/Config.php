<?php
/**
 * User: admin
 * Date: 2024/1/18
 */


namespace app\admin\controller\setting;

use app\admin\controller\Base;
use app\admin\model\Config as ConfigModel;
use app\admin\model\ConfigGroup as ConfigGroupModel;
use app\admin\validate\Config as ConfigValidate;
use app\common\traits\ConfigTrait;
use think\facade\View;

class Config extends Base
{
    use ConfigTrait;

    public function test()
    {
        //$res = $this->getConfig('admin.base');
        //halt($res);
    }

    public function index()
    {
        $configs = ConfigModel::with('config_group')
            ->paginate($this->admin['pagesize']);

        View::assign([
            'configs'=>$configs,
            'total'=>$configs->total(),
            'page'=>$configs->render()
        ]);

        return View::fetch();
    }

    public function add(ConfigValidate $validate)
    {
        if($this->request->isPost())
        {
            $param = $this->request->param();
            if(!empty($param))
            {
                $check = $validate->check($param);
                if(!$check)
                    return $this->error($validate->getError());

                try
                {
                    //设置 各配置项
                    $param = $this->setConfigContent($param);
                }
                catch (\Exception $exception)
                {
                    return $this->error($exception->getMessage());
                }

                //避免配置和系统变量或者文件名出现重复冲突，加一下前缀
                //$param['code'] = add_prefix($param['code']);

                $res = ConfigModel::create($param);
                if($res)
                {
                    //创建生成配置文件
                    $group = ConfigGroupModel::find($res->group_id);
                    if($group && $group->auto_create_config)
                        $this->writeConfigFile($group->module);
                    return $this->success('添加成功', 200,[], URL_BACK);
                }
                else
                {
                    return $this->error('添加失败',500);
                }
            }
        }
        else
        {
            $configGroup = ConfigGroupModel::select();
            View::assign([
                'configGroup' => $configGroup
            ]);

            return View::fetch();
        }

    }

    public function edit(ConfigValidate $validate)
    {
        $param = $this->request->param();
        $id = $param['id'];
        $config = ConfigModel::find($id);
        if($this->request->isPost())
        {
            $check = $validate->check($param);
            if(!$check)
                return $this->error($validate->getError());

            try
            {
                //设置 各配置项
                $param = $this->setConfigContent($param);
            }
            catch (\RuntimeException $exception)
            {
                return $this->error($exception->getMessage());
            }

            //避免配置和系统变量或者文件名出现重复冲突，加一下前缀
            //$param['code'] = add_prefix($param['code']);

            $res = $config->save($param);
            if($res)
            {
                //更新配置文件
                $group = ConfigGroupModel::find($config->group_id);
                if($group && $group->auto_create_config)
                    $this->writeConfigFile($group->module);
                return $this->success('修改成功',200, [], 'index');
            }
            else
                return $this->success('修改失败');
        }
        else
        {
            $configGroup = ConfigGroupModel::select();
            View::assign([
                'config' => $config,
                'configGroup' => $configGroup
            ]);
            return View::fetch();
        }
    }


    public function del(ConfigModel $model)
    {
        $param = $this->request->param();
        $id = $param['id'];
        $unDeleteAble = $model->inNoDeletionIds($id);
        $unDelIds = "";
        if(!is_array($unDeleteAble))
        {
            if($unDeleteAble)
                return $this->error('不可删除记录，系统保留用');
        }
        else
        {
            $id = $unDeleteAble['diff'];
            $unDelIds = implode(',', $unDeleteAble['intersect']);
        }
        $configs = $model->whereIn('id', $id)->select();

        $res = $model->destroy(static function ($query) use ($id){
            /** @var \think\db\Query $query */
            $query->whereIn('id', $id);
        });
        if($res)
        {
            //更新配置文件
            $groupIds = [];
            foreach ($configs as $config)
            {
                if(!in_array($config['group_id'], $groupIds))
                    $groupIds[] =$config['group_id'];
            }
            $groups = ConfigGroupModel::whereIn('id', $groupIds)->select();
            foreach ($groups as $group)
            {
                if($group && $group->auto_create_config)
                    $this->writeConfigFile($group->module);
            }

            if($unDelIds != '')
                return $this->error('删除失败，部分未删除ID:' . $unDelIds, 500, [], URL_RELOAD);
            else
                return $this->success('删除成功', 200, [], URL_RELOAD);
        }
        else
        {
            return $this->error('删除失败');
        }
    }

    public function update()
    {
        if($this->request->isPost())
        {
            $param = $this->request->param();
            $id = $param['id'];
            $config = ConfigModel::find($id);

            //更新内容值
            $content = [];
            foreach ($config->content as $value)
            {
                if(isset($param[$value['field']]))// && $param[$value['field']] != ''
                {
                    $value['value'] = $param[$value['field']];
                    $content[] = $value;
                }

            }
            $config->content = $content;
            //halt($config);
            $res = $config->save();
            if($res)
            {
                //更新配置文件
                $group = ConfigGroupModel::find($config->group_id);
                if($group && $group->auto_create_config)
                    $this->writeConfigFile($group->module);
                return $this->success('修改成功',200, [], url:URL_RELOAD);
            }
            else
                return $this->success('修改失败');
        }
    }

    public function show($code)
    {
        $code = add_prefix($code);//加前缀
        $configs = ConfigModel::hasWhere('configGroup', ['code'=>$code])->select();
        View::assign([
            'configs'=> $configs,
        ]);
        //halt($configs);
        return View::fetch('show');

    }

    /**
     * 后台配置
     * @return string
     */
    public function config()
    {
        $code = 'admin';//该值是配置分组的code代码字符串
        return $this->show($code);
    }

    public function indexConfig()
    {
        $code = 'index';//该值是配置分组的code代码字符串
        return $this->show($code);
    }


}