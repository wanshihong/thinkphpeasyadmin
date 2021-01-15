function renderSelect2($, select, xmSelect) {
    let dataset = select.data();
    let id = select.attr('id');
    let selectInput = xmSelect.render({
        el: `#${id}`,
        radio: true,
        name: dataset.name,
        clickClose: true,
        filterable: true,
        tips: dataset.placeholder,
        autoRow: true,
        toolbar: {
            show: true,
            list: ['CLEAR']
        },
        remoteSearch: true,
        remoteMethod: function (val, cb) {
            //这里如果val为空, 则不触发搜索
            if (!val) {
                return cb([]);
            }
            $.get(dataset.url, {
                search: val,
                pk: dataset.pk,
                table: dataset.table,
                property: dataset.property,
            }, data => {
                if (!data.code) {
                    layer.msg(data.msg, {icon: 2});
                    return;
                }
                cb(data.data);
            })
        }
    })

    if (dataset.value) {
        $.get(dataset.url, {
            pk: dataset.pk,
            table: dataset.table,
            property: dataset.property,
            default: dataset.value,
        }, data => {
            if (!data.code) {
                layer.msg(data.msg, {icon: 2});
                return;
            }
            let row = data.data[0];
            row.selected = true;
            selectInput.update({
                data: [row],
                autoRow: true,
            });
        })
    }
}

layui.config({base: '/easy_admin_static/layui-v2.5.7/lay/modules/'}).extend({
    xmSelect: 'xm-select/xm-select'
}).use(['xmSelect', 'jquery'], function () {
    let xmSelect = layui.xmSelect, $ = layui.jquery;

    $('.select-input').each(function () {
        renderSelect2($, $(this), xmSelect);
    });
});
