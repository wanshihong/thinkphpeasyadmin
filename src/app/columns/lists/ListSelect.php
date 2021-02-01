<?php


namespace easyadmin\app\columns\lists;

/**
 * 下拉选择
 * Class ListChois
 * @package easyadmin\app\columns\lists
 */
class ListSelect extends BaseList
{


    /**
     * 列的其他选项
     * @var array
     */
    protected $options = [
        'attr' => '',//属性
        'class' => '',//样式表
        'success' => 1,
        'style' => '',
        // options 中的 color 支持 ['gray', 'black', 'blue', 'cyan', 'green', 'orange', 'red']
        'options' => [
//            ['key' => 'key', 'text' => 'text','color'=>'red'],
        ]
    ];

    /**
     * 字段的模板路径
     * @var string
     */
    protected $templatePath = 'list:field:select';

    public function formatData($data)
    {

        foreach ($this->getOption('options') as $option) {
            //值匹配上了
            if ($option['key'] == $data['value']) {
                $color = isset($option['color']) ? $option['color'] : '';
                $data['text'] = $option['text'];

                //如果有颜色
                if (empty($color)) break;
                $data['attr'] .= " style=background-color:{$color}";
                break;
            }
        }

        //未找到匹配的值
        if (!isset($data['text'])) {
            $data['text'] = '-';
            $data['class'] .= ' layui-bg-gray';
        }

        return $data;
    }

}
