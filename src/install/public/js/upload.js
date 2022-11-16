let MyUpload = {
    checkImage: function (type) {
        if (!type) return false;
        let arr = type.split('/');
        if (!arr) return false;
        return (arr[0] === 'image');

    },


    dataURLtoFile: function (dataurl, filename) {
        let arr = dataurl.split(','),
            mime = arr[0].match(/:(.*?);/)[1],
            bstr = atob(arr[1]),
            n = bstr.length,
            u8arr = new Uint8Array(n);
        while (n--) {
            u8arr[n] = bstr.charCodeAt(n);
        }
        let hz = '.png';
        if (mime) {
            let tempArr = mime.split('/');
            if (tempArr) {
                hz = '.' + tempArr[1];
            }
        }
        return new File([u8arr], filename + hz, {type: mime});
    },

    uploadImg: function ($self, layer, formData, layerCropperIndex) {
        let loadIndex = layer.load();
        let id_prev = $self.data('id');
        let url = $self.data('url');
        $.ajax({
            url: url,
            type: 'POST',
            cache: false,
            data: formData,
            processData: false,
            contentType: false
        }).success(function (res) {
            if (res.errno !== 0) {
                layer.msg(res.msg, {icon: 2});
                return false
            }

            if (res.data[0].alipay) {
                let alipay = res.data[0].alipay;
                $(`#${id_prev}_input`).val(alipay['resource_id']);
                $self.html(`<img src="${alipay['resource_url']}" style="width: 100%;height: 100%;" alt=""/>`);
            } else {
                let path = res.data[0];
                $(`#${id_prev}_input`).val(path);

                $self.html(`<img src="${path}" style="width: 100%;height: 100%;" alt=""/>`);
            }


            layer.close(loadIndex);
            layer.close(layerCropperIndex);
        }).fail(function (res) {
            layer.msg(res.msg);
        });
    },

    cropper: function (file, $self, layer) {
        let id_prev = $self.data('id');
        let width = $self.data('width');
        let height = $self.data('height');
        let fread = new FileReader();
        let cropper;
        fread.onloadend = function () {
            let src = this.result;
            let layerCropperIndex = layer.open({
                title: '图片裁剪',
                area: ['100vw', '100vh'],
                shade: '0.35',
                offset: 'auto',
                content: `
                    <div style="width: 100%;height: 100%;display: flex;flex-direction: column;justify-content: center;align-items: center;">
                        <img id="${id_prev}_preview" src="${src}" alt="" style="width: 100%;">
                    </div>
                    <p>鼠标滚轮可放大缩小图片</p>
                `,
                success: function () {
                    const image = document.getElementById(`${id_prev}_preview`);
                    cropper = new Cropper(image, {
                        aspectRatio: width / height,
                    });
                },
                btn: ['确定'],
                yes: function () {
                    //按钮【按钮一】的回调
                    let canvasData = cropper.getCroppedCanvas();
                    let base64Image = canvasData.toDataURL();

                    // base64  图片转成 file 对象
                    let file = MyUpload.dataURLtoFile(base64Image, 'ttt');


                    //构建 formData 上传文件
                    let formData = new FormData();
                    formData.append('file', file);
                    MyUpload.uploadImg($self, layer, formData, layerCropperIndex);
                }

            });


        }
        fread.readAsDataURL(file);
    }

}


layui.use(['jquery', 'layer'], function () {
    let $ = layui.jquery, layer = layui.layer;


    $('.upload-img-box').click(function () {
        let $self = $(this);
        let isCropper = $self.data('cropper');
        let id = $self.data('id');

        let className = `upload-img-file-input${id}`;
        let selectName = `.upload-img-file-input${id}`;
        let fileInput = $(selectName);

        if (!fileInput.length) {
            $('body').append(`<input type='file' class='${className}' style='position: fixed;left: -5000px;' />`);
            fileInput = $(selectName);
            fileInput.change(function () {
                let file = $(this)[0].files[0];
                if (!file) return;

                //如果是图片类型 , 并且开启了裁剪
                if (MyUpload.checkImage(file.type) && isCropper) {
                    MyUpload.cropper(file, $self, layer)
                } else {
                    let formData = new FormData();
                    formData.append('file', file);
                    MyUpload.uploadImg($self, layer, formData, 0)
                }

            });
        }


        fileInput.click();

    });


});
