layui.use(['laydate','jquery'], function () {
    let laydate = layui.laydate,$ = layui.jquery;

    $('.lay-date').each(function (){
        let options = $(this).data('options');
        options['elem'] = '.'+$(this).data('render')
        laydate.render(options);
    });



});
