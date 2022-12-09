layui.use(['layer', 'jquery'], function () {
    let layer = layui.layer;
    let $ = layui.jquery;

    $(".easy_admin_copy_text").click(function () {
        let text = $(this).text();

        if (text) {
            text = text.trim();
        }

        if (text) {
            clipboard.writeText(text)
            layer.msg('复制成功');
        }
    });


});