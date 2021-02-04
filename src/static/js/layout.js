var $;


function btnConfirm(btn) {
    let dataset = btn.dataset;
    let index = layer.confirm(dataset.confirm, {
        btn: ['确定', '取消']
    }, function () {
        layer.close(index);

        if (!dataset.href) return;
        $.get(dataset.href, res => {
            if (res.code) {
                layer.msg('操作成功', {icon: 1}, function () {
                    if (dataset.referer) {
                        window.location.href = dataset.referer;
                    }
                });
                $(btn).closest('tr').remove();
            } else {
                layer.msg(res.msg, {icon: 2});
            }
        })
    });
}

//点击操作按钮
function clickBtn(btn) {
    let dataset = btn.dataset;
    if (dataset.confirm) {
        btnConfirm(btn);
    } else if (dataset.href) {
        if(dataset.referer){
            window.location.href = dataset.referer;
        }else{
            window.location.href = dataset.href;
        }
        return false;
    }

}

let layerLoadIndex;
layui.use(['element', 'layer', 'jquery', 'form'], function () {
    //var element = layui.element;
    $ = layui.jquery;

    $.ajaxSetup({
        beforeSend: function () {
            if (!layerLoadIndex) {
                layerLoadIndex = layer.load();
            }
        },
        complete: function () {
            layer.close(layerLoadIndex);
            layerLoadIndex = undefined;
        }
    })

    //显示导航
    $('.show-menu').click(function () {
        $('.layout-left').addClass('float-menu');
    });

    //关闭导航
    $('.close-menu').click(function () {
        $('.layout-left').removeClass('float-menu');
    });

    //点击图片 查看大图
    $('.list-img').click(function () {
        let src = $(this).attr('src');
        layer.open({
            type: 1,
            title: false,
            area: ['100vw', '100vh'],
            shade: '0.35',
            offset: 'auto',
            content: `<div style="width: 100%;height: 100%;background-color: rgba(0,0,0,.35);display: flex;justify-content: center;align-items: center">
                            <img style="border-radius: 4%;max-width: 98%;max-height: 98%;" src="${src}" alt="查看大图"/>
                       </div>`,
            success: function (layero, index) {
                $(layero).css({
                    background: 'rgba(0,0,0,.35)'
                })
                $(layero).find('.layui-layer-close').css({
                    top: '0',
                    right: '0',
                })
            }
        });
    });

});
