layui.use(['jquery', 'layer'], function () {
    $ = layui.jquery;


    $('.editor-div').each(function () {
        let E = window.wangEditor
        let editor = new E('#' + $(this).attr('id'))
        let $text1 = $('#' + $(this).data('target'))

        editor.config.uploadImgServer = $(this).data('url')


        editor.config.onchange = function (html) {
            // 第二步，监控变化，同步更新到 textarea
            $text1.val(html)
        }


        //重写消息提示
        editor.config.customAlert = function (s, t) {

            switch (t) {
                case 'success':
                    layer.msg(s, {icon: 1});
                    break
                case 'info':
                    layer.msg(s, {icon: 4});
                    break
                case 'warning':
                    layer.msg(s, {icon: 3});
                    break
                case 'error':
                    layer.msg(s, {icon: 2});
                    break
                default:
                    layer.msg(s);
                    break
            }
        }

        
        editor.create()

        // 第一步，初始化 textarea 的值
        editor.txt.html($text1.val())
        $text1.val(editor.txt.html())
    });

});
