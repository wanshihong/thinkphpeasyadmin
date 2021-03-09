<?php


namespace easyadmin\app\columns\lists;


class ListText extends BaseList
{
    /**
     * 字段的模板路径
     * @var string
     */
    protected $templatePath = 'list:field:text';

    public function formatData($data)
    {
        $edit = $this->getOption('edit', 0);
        $data['edit'] = $edit;
        if($edit){
            $params = $this->getOption('params', []);
            $params['id'] = $data['row_id'];
            $params['field'] = $data['field'];
            $url = url($this->getOption('url','enable'),$params);
            $data['url'] = $url;
            $data['type'] = $this->getOption('type','text');
        }

        return $data;
    }

}
