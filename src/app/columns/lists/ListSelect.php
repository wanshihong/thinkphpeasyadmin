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
            if ($option['key'] == $data['value']) {
                $color = isset($option['color']) ? $option['color'] : '';
                $data['text'] = $option['text'];
                if (empty($color)) break;

                if (in_array($color, ['gray', 'black', 'blue', 'cyan', 'green', 'orange', 'red'])) {
                    $color = $color == 'red' ? '' : $color;
                    $data['class'] .= ' layui-bg-' . $color;
                } else {
                    $data['style'] = 'background-color:' . $color;
                }

                break;
            }
        }

        return $data;
    }

}
