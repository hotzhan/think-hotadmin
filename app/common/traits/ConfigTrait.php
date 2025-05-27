<?php
/**
 * User: admin
 * Date: 2024/1/25
 */


namespace app\common\traits;

use app\admin\model\Config as ConfigModel;
use app\admin\model\ConfigGroup as ConfigGroupModel;
use RuntimeException;
use think\facade\Config;

trait ConfigTrait
{
    /**
     * 设置配置content
     * @param array $params
     * @return array
     */
    protected function setConfigContent(array $params): array
    {
        //[{"name":"xxx","type":"xxx","field":"xxx","value":"xxx","options":"xxx","desc":"xxx"},{},...];
        $content = [];
        if(isset($params['config_content_type']) && !empty($params['config_content_type']))
        {
            for($i = 0; $i < count($params['config_content_type']); $i++)
            {
                if($params['config_content_name'][$i] === '' ||
                    $params['config_content_type'][$i] === '' ||
                    $params['config_content_field'][$i] === ''
                )
                {
                    throw new RuntimeException('设置信息不完整');
                }
                $con = [
                    'name'=>$params['config_content_name'][$i],
                    'type'=>$params['config_content_type'][$i],
                    'field'=>$params['config_content_field'][$i],
                    'value'=>$params['config_content_value'][$i],
                    'options'=>$params['config_content_options'][$i],
                    'desc'=>$params['config_content_desc'][$i]
                ];

                //$content[] = json_encode($con);

                //ConfigGroup模型model里已经设置了content字段是json，会自动转换
                $content[] = $con;
            }

            unset($params['config_content_name']);
            unset($params['config_content_type']);
            unset($params['config_content_field']);
            unset($params['config_content_value']);
            unset($params['config_content_options']);
            unset($params['config_content_desc']);
        }


        $params['content'] = $content;

        return $params;
    }

    public function getConfig(string $key)
    {
        if(!str_starts_with($key, "hot_"))
            $key = 'hot_' . $key;//加一下前缀
        $config = Config::get($key);//如果有配置文件，直接读取配置文件
        if(!$config)
        {
            //配置文件不存在，读取数据库
            $keyArr = explode('.', $key);
            //配置文件不存在，读取数据库配置
            $len = count($keyArr);
            switch ($len)
            {
                case 1://返回整个组的配置
                    $groupCode = $keyArr[0];
                    $data = ConfigGroupModel::where('code', $groupCode)->with('config')->find();
                    $config = $this->toConfigData($data->config);
                    break;
                case 2://返回某个配置块的各项配置
                    $groupCode = $keyArr[0];
                    $configCode = $keyArr[1];
                    $group = ConfigGroupModel::where('code', $groupCode)->find();
                    $configData = ConfigModel::where('group_id', $group['id'])
                        ->where('code', $configCode)
                        ->select();
                    $config = $this->toConfigData($configData);
                    break;
                case 3://返回某个配置值
                    $groupCode = $keyArr[0];
                    $configCode = $keyArr[1];
                    $field = $keyArr[2];
                    /*这里其实也是进行了两次查询，而且变量$configCode还不好传进去
                     * $data = ConfigGroupModel::where('code', $groupCode)->with(['config'=>function(Query $query){
                        $query->where('code', $configCode);
                    }])->find();
                    */
                    $group = ConfigGroupModel::where('code', $groupCode)->find();
                    $configData = ConfigModel::where('group_id', $group['id'])
                        ->where('code', $configCode)
                        ->select();
                    $config = $this->toConfigData($configData);
                    $config = $config[$configCode][$field];
                    break;
                default:
                    return '';
            }
        }
        //dump($config);
        return $config;

    }
    public function getConfig2(string $module, string $code, string $field)
    {
        //先读取配置文件里的，如果没有配置文件，读取数据库里的
        $moduleConfigPath = app()->getBasePath() . $module . '/config/';
        $configModule = add_prefix($module);
        $configCode = add_prefix($code);
    }

    public function toConfigData($data)
    {
        $array = [];
        foreach ($data as $config)
        {
            //-------每个配置块-------start
            $contentArr = [];
            //-------每个配置块里的配置-------start
            foreach ($config->content as $content)
            {
                $contentArr[$content['field']] = $content['value'];
            }
            //-------每个配置块里的配置-------end

            $array[$config->code] = $contentArr;
            //-------每个配置块-------start
        }
        return $array;
    }

    /**
     * 后台自定义配置写到配置文件
     * @param string $module
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function writeConfigFile(string $module)
    {
        $group = ConfigGroupModel::where('module', $module)
            ->with('config')
            ->find();

        $text = "<?php\r\nreturn [\r\n";//开始
        foreach ($group->config as $config)
        {
            //-------每个配置块-------start
            //dump($config->toArray());
            $text .= "\t//{$config->name}\r\n";  //注释行
            $text .= "\t'{$config->code}' => [\r\n";

            //-------每个配置块里的配置-------start
            foreach ($config->content as $content)
            {
                $text .= "\t\t//{$content['name']}\r\n";  //注释行
                $text .= "\t\t'{$content['field']}' => '{$content['value']}',\r\n";
            }

            //-------每个配置块里的配置-------end

            $text .= "\t],\r\n\r\n";
            //-------每个配置块-------start
        }
        $text .= "];\r\n";//结束

        $moduleConfigPath = app()->getBasePath() . $module . '/config/';
        if(!file_exists($moduleConfigPath) || !is_dir($moduleConfigPath))
            mkdir($moduleConfigPath, 0755);
        $configFile = $moduleConfigPath . $group->code . '.php';
        file_put_contents($configFile, $text);
    }

    public function deleteConfigFile(string $module)
    {
        $group = ConfigGroupModel::where('module', $module)
            ->with('config')
            ->find();

        $moduleConfigPath = app()->getBasePath() . $module . '/config/';
        $configFile = $moduleConfigPath . $group->code . '.php';
        if(file_exists($configFile))
            unlink($configFile);
    }
}