layui.use(['form', 'jquery', 'layer'], function () {
    let form = layui.form, $ = layui.jquery, layer = layui.layer;


    form.on('submit(easy_form)', function (data) {
        console.log(data.elem) //被执行事件的元素DOM对象，一般为button对象
        console.log(data.form) //被执行提交的form对象，一般在存在form标签时才会返回
        console.log(data.field) //当前容器的全部表单字段，名值对形式：{name: value}

        $.post("form_save", data.field, res => {
            if (res.code) {
                layer.msg('操作成功', {icon: 1});
                window.location.href = res.ref ? res.ref : 'lists';
            } else {
                layer.msg(res.msg, {icon: 2});
            }
        })

        return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
    });


});
