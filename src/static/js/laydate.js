layui.use(['laydate','jquery'], function () {
    let laydate = layui.laydate,$ = layui.jquery;

    $('.lay-date').each(function (){
        let options = $(this).data('options');
        options['elem'] = '.'+$(this).data('render')
        if(options['range']){
            if(options['type']==='datetime'){
                $(this).css('minWidth','300px');
            }else{
               // $(this).css('minWidth','300px');
            }

        }
        laydate.render(options);
    });



});
