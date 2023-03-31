function renderSelect2($, select, xmSelect) {
    let dataset = select.data();
    console.log(dataset);
    let id = select.attr('id');
    let multiple = dataset.multiple;

    let selectInput = xmSelect.render({
        el: `#${id}`,
        radio: multiple!==1,
        name: dataset.name,
        clickClose: true,
        filterable: true,
        tips: dataset.placeholder,
        searchTips: '请输入关键词进行搜索',
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
                field: dataset.name,
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
            field: dataset.name,
            pk: dataset.pk,
            table: dataset.table,
            property: dataset.property,
            default: dataset.value,
        }, data => {
            if (!data.code) {
                layer.msg(data.msg, {icon: 2});
                return;
            }
            // let row = data.data[0];
            // row.selected = true;
            selectInput.update({
                data: data.data,
                autoRow: true,
            });
        })
    }
}

layui.config({base: `${STATIC_ROOT}layui-v2.5.7/lay/modules/`}).extend({
    xmSelect: 'xm-select/xm-select'
}).use(['xmSelect', 'jquery'], function () {
    let xmSelect = layui.xmSelect, $ = layui.jquery;

    $('.select-input').each(function () {
        renderSelect2($, $(this), xmSelect);
    });
});
