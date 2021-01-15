layui.use(['form','layer'], function () {
    let form = layui.form,layer = layui.layer;
    //启用,禁用;
    form.on('switch(list_switch)', function (data) {
        let value = data.elem.checked ? 1 : 0;
        let dataset = data.elem.dataset;

        $.post(dataset.url, {
            value: value,
        }, res => {
            if (res.code) {
                layer.msg('操作成功', {icon: 1});
            } else {
                layer.msg(res.msg, {icon: 2});
            }
        });

    });

});
